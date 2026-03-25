<?php

namespace Modules\Leads\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\StreamedResponse;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Campaigns\Models\Campaign;
use Modules\Leads\Models\Lead;

class LeadIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $ratingFilter = '';

    public string $ownerFilter = '';

    public string $campaignFilter = '';

    public string $leadSourceFilter = '';

    public string $convertedFilter = '';

    /**
     * @var array<int, string>
     */
    public array $selected = [];

    public string $bulkOwnerId = '';

    public string $bulkStatus = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('leads.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCampaignFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLeadSourceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingConvertedFilter(): void
    {
        $this->resetPage();
    }

    public function toggleSelection(string $id): void
    {
        if (in_array($id, $this->selected, true)) {
            $this->selected = array_values(array_filter(
                $this->selected,
                fn (string $selectedId): bool => $selectedId !== $id
            ));

            return;
        }

        $this->selected[] = $id;
    }

    public function convertLead(string $id): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        $lead = Lead::query()->findOrFail($id);
        $lead->convert(auth()->user());

        session()->flash('status', 'Lead converted successfully.');
        $this->resetPage();
    }

    public function deleteLead(string $id): void
    {
        abort_unless(auth()->user()?->can('leads.delete'), 403);

        Lead::query()->whereKey($id)->delete();

        session()->flash('status', 'Lead deleted.');
        $this->resetPage();
    }

    public function bulkAssignOwner(): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        if ($this->selected === [] || $this->bulkOwnerId === '') {
            return;
        }

        Lead::query()
            ->whereIn('id', $this->selected)
            ->update(['owner_id' => $this->bulkOwnerId]);

        session()->flash('status', 'Owner assigned to selected leads.');
    }

    public function bulkChangeStatus(): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        if ($this->selected === [] || $this->bulkStatus === '') {
            return;
        }

        Lead::query()
            ->whereIn('id', $this->selected)
            ->update([
                'status' => $this->bulkStatus,
                'converted' => $this->bulkStatus === 'Converted',
                'converted_at' => $this->bulkStatus === 'Converted' ? now() : null,
            ]);

        session()->flash('status', 'Status updated for selected leads.');
    }

    public function bulkDelete(): void
    {
        abort_unless(auth()->user()?->can('leads.delete'), 403);

        if ($this->selected === []) {
            return;
        }

        Lead::query()->whereIn('id', $this->selected)->delete();
        $this->selected = [];

        session()->flash('status', 'Selected leads deleted.');
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->can('leads.export'), 403);

        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, ['Name', 'Company', 'Source', 'Status', 'Score', 'Rating', 'Owner', 'Created']);

            $this->filteredQuery()
                ->select(['first_name', 'last_name', 'company', 'lead_source', 'status', 'score', 'rating', 'owner_id', 'created_at'])
                ->with('owner:id,full_name')
                ->orderByDesc('created_at')
                ->chunk(250, function ($rows) use ($stream): void {
                    foreach ($rows as $lead) {
                        fputcsv($stream, [
                            $lead->full_name,
                            $lead->company,
                            $lead->lead_source,
                            $lead->status,
                            $lead->score,
                            $lead->rating,
                            $lead->owner?->full_name,
                            $lead->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($stream);
        }, 'leads-'.now()->format('Ymd-His').'.csv');
    }

    public function render(): View
    {
        $query = $this->filteredQuery()
            ->select([
                'id',
                'first_name',
                'last_name',
                'company',
                'lead_source',
                'status',
                'score',
                'rating',
                'owner_id',
                'campaign_id',
                'converted',
                'created_at',
            ])
            ->with('owner:id,full_name')
            ->orderByDesc('created_at');

        if (class_exists(Campaign::class)) {
            $query->with('campaign:id,name');
        }

        $leads = $query->paginate(15);

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $campaigns = collect();

        if (class_exists(Campaign::class)) {
            $campaigns = Campaign::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        return view('leads::livewire.lead-index', [
            'campaigns' => $campaigns,
            'leads' => $leads,
            'leadSources' => ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'],
            'owners' => $owners,
            'ratings' => ['Hot', 'Warm', 'Cold'],
            'statuses' => ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'],
        ])->extends('core::layouts.module', ['title' => 'Leads']);
    }

    protected function filteredQuery(): Builder
    {
        return Lead::query()
            ->when($this->search !== '', fn (Builder $query) => $query->search($this->search))
            ->when($this->statusFilter !== '', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->ratingFilter !== '', fn (Builder $query) => $query->where('rating', $this->ratingFilter))
            ->when($this->ownerFilter !== '', fn (Builder $query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->campaignFilter !== '', fn (Builder $query) => $query->where('campaign_id', $this->campaignFilter))
            ->when($this->leadSourceFilter !== '', fn (Builder $query) => $query->where('lead_source', $this->leadSourceFilter))
            ->when($this->convertedFilter !== '', fn (Builder $query) => $query->where('converted', $this->convertedFilter === '1'));
    }
}
