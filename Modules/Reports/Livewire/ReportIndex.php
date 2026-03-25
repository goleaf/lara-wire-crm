<?php

namespace Modules\Reports\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Reports\Models\Report;

class ReportIndex extends Component
{
    use WithPagination;

    public string $moduleFilter = '';

    public string $typeFilter = '';

    public string $ownerFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);
    }

    public function updatingModuleFilter(): void
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

    public function deleteReport(string $id): void
    {
        abort_unless(auth()->user()?->can('reports.delete'), 403);

        Report::query()->whereKey($id)->delete();

        session()->flash('status', 'Report deleted.');
    }

    public function render(): View
    {
        $reports = Report::query()
            ->select(['id', 'name', 'type', 'module', 'owner_id', 'is_public', 'created_at'])
            ->with(['owner:id,full_name'])
            ->when($this->moduleFilter !== '', fn ($query) => $query->where('module', $this->moduleFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports::livewire.report-index', [
            'modules' => ['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products'],
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'reports' => $reports,
            'types' => ['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area'],
        ])->extends('core::layouts.module', ['title' => 'Reports']);
    }
}
