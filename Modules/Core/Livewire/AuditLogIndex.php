<?php

namespace Modules\Core\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\AuditLog;

class AuditLogIndex extends Component
{
    use WithPagination;

    public string $userFilter = '';

    public string $modelFilter = '';

    public string $actionFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->user()?->hasPermission('view'), 403);
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModelFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
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
        $logs = AuditLog::query()
            ->select(['id', 'user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values', 'ip_address', 'created_at'])
            ->with(['user:id,full_name'])
            ->when($this->userFilter !== '', fn ($query) => $query->where('user_id', $this->userFilter))
            ->when($this->modelFilter !== '', fn ($query) => $query->where('model_type', $this->modelFilter))
            ->when($this->actionFilter !== '', fn ($query) => $query->where('action', $this->actionFilter))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('core::livewire.audit-log-index', [
            'actions' => ['created', 'updated', 'deleted'],
            'models' => AuditLog::query()->select(['model_type'])->distinct()->pluck('model_type')->all(),
            'users' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'logs' => $logs,
        ])->extends('core::layouts.module', ['title' => 'Audit Logs']);
    }
}
