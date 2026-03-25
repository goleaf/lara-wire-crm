<?php

namespace Modules\Deals\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\PipelineStage;

class DealList extends Component
{
    use WithPagination;

    public string $sortBy = 'close_date';

    public string $sortDirection = 'asc';

    public string $ownerFilter = '';

    public string $stageFilter = '';

    /**
     * @var array<int, string>
     */
    public array $selected = [];

    public string $bulkOwnerId = '';

    public string $bulkStageId = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('deals.view'), 403);
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortBy = $field;
        $this->sortDirection = 'asc';
    }

    public function updateStage(string $dealId, string $stageId): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        Deal::query()->whereKey($dealId)->update(['stage_id' => $stageId]);

        session()->flash('status', 'Deal stage updated.');
    }

    public function toggleSelection(string $id): void
    {
        if (in_array($id, $this->selected, true)) {
            $this->selected = array_values(array_filter($this->selected, fn (string $selected): bool => $selected !== $id));

            return;
        }

        $this->selected[] = $id;
    }

    public function bulkAssignOwner(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        if ($this->selected === [] || $this->bulkOwnerId === '') {
            return;
        }

        Deal::query()->whereIn('id', $this->selected)->update(['owner_id' => $this->bulkOwnerId]);

        session()->flash('status', 'Owner assigned to selected deals.');
    }

    public function bulkChangeStage(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        if ($this->selected === [] || $this->bulkStageId === '') {
            return;
        }

        Deal::query()->whereIn('id', $this->selected)->update(['stage_id' => $this->bulkStageId]);

        session()->flash('status', 'Stage changed for selected deals.');
    }

    public function render(): View
    {
        $sortable = ['name', 'amount', 'close_date', 'probability'];
        $sortBy = in_array($this->sortBy, $sortable, true) ? $this->sortBy : 'close_date';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $deals = Deal::query()
            ->select([
                'id',
                'name',
                'account_id',
                'owner_id',
                'stage_id',
                'amount',
                'probability',
                'close_date',
            ])
            ->with([
                'account:id,name',
                'owner:id,full_name',
                'stage:id,name',
            ])
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->stageFilter !== '', fn ($query) => $query->where('stage_id', $this->stageFilter))
            ->orderBy($sortBy, $sortDirection)
            ->paginate(15);

        return view('deals::livewire.deal-list', [
            'deals' => $deals,
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'stages' => PipelineStage::query()->select(['id', 'name'])->orderBy('order')->get(),
        ])->extends('core::layouts.module', ['title' => 'Deals List']);
    }
}
