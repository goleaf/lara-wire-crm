<?php

namespace Modules\Cases\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;

class CaseIndex extends Component
{
    use WithPagination;

    public string $viewMode = 'table';

    public string $statusFilter = '';

    public string $priorityFilter = '';

    public string $typeFilter = '';

    public string $ownerFilter = '';

    public string $overdueFilter = '';

    public string $accountFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('cases.view'), 403);
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['table', 'kanban'], true) ? $mode : 'table';
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
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

    public function updatingOverdueFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $baseQuery = SupportCase::query()
            ->select([
                'id',
                'number',
                'title',
                'account_id',
                'contact_id',
                'priority',
                'status',
                'type',
                'owner_id',
                'sla_deadline',
                'satisfaction_score',
                'created_at',
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name',
                'owner:id,full_name',
            ])
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->priorityFilter !== '', fn ($query) => $query->where('priority', $this->priorityFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->accountFilter !== '', fn ($query) => $query->where('account_id', $this->accountFilter))
            ->when($this->overdueFilter === '1', fn ($query) => $query->overdue())
            ->when($this->overdueFilter === '0', fn ($query) => $query->where(function ($inner): void {
                $inner
                    ->whereNull('sla_deadline')
                    ->orWhere('sla_deadline', '>=', now())
                    ->orWhereIn('status', ['Resolved', 'Closed']);
            }));

        $summaryRows = (clone $baseQuery)->get(['id', 'status', 'sla_deadline']);
        $resolvedToday = (clone $baseQuery)->where('status', 'Resolved')->whereDate('resolved_at', now()->toDateString())->count();

        $summary = [
            'open' => $summaryRows->where('status', 'Open')->count(),
            'in_progress' => $summaryRows->where('status', 'In Progress')->count(),
            'pending' => $summaryRows->where('status', 'Pending')->count(),
            'resolved_today' => $resolvedToday,
            'sla_breached' => $summaryRows->filter(fn ($case) => $case->sla_deadline !== null && $case->sla_deadline->isPast() && ! in_array($case->status, ['Resolved', 'Closed'], true))->count(),
        ];

        $cases = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->paginate(15);

        $kanbanCases = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->groupBy('status');

        return view('cases::livewire.case-index', [
            'accounts' => Account::query()->select(['id', 'name'])->orderBy('name')->get(),
            'cases' => $cases,
            'kanbanCases' => $kanbanCases,
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'priorities' => ['Low', 'Medium', 'High', 'Critical'],
            'statuses' => ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'],
            'summary' => $summary,
            'types' => ['Bug', 'Feature Request', 'Question', 'Complaint', 'Other'],
        ])->extends('core::layouts.module', ['title' => 'Support Cases']);
    }
}
