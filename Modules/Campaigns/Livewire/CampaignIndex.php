<?php

namespace Modules\Campaigns\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Campaigns\Models\Campaign;

class CampaignIndex extends Component
{
    use WithPagination;

    public string $viewMode = 'cards';

    public string $statusFilter = '';

    public string $typeFilter = '';

    public string $ownerFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('campaigns.view'), 403);
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['cards', 'table'], true) ? $mode : 'cards';
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $campaigns = Campaign::query()
            ->select([
                'id',
                'name',
                'type',
                'status',
                'start_date',
                'end_date',
                'budget',
                'actual_cost',
                'expected_leads',
                'owner_id',
            ])
            ->with('owner:id,full_name')
            ->withCount('leads')
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('start_date', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('end_date', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('campaigns::livewire.campaign-index', [
            'campaigns' => $campaigns,
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'types' => ['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program', 'Other'],
            'statuses' => ['Planned', 'Active', 'Completed', 'Paused'],
        ])->extends('core::layouts.module', ['title' => 'Campaigns']);
    }
}
