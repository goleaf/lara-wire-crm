<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Reports\Models\Dashboard as DashboardModel;

class DashboardIndex extends Component
{
    use WithPagination;

    public string $name = '';

    public bool $isPublic = false;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function createDashboard(): void
    {
        abort_unless(auth()->user()?->can('reports.create'), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'isPublic' => ['boolean'],
        ]);

        DashboardModel::query()->create([
            'name' => $validated['name'],
            'owner_id' => auth()->id(),
            'is_default' => false,
            'is_public' => (bool) $validated['isPublic'],
            'layout' => [],
        ]);

        $this->reset(['name', 'isPublic']);
        $this->resetPage();

        session()->flash('status', 'Dashboard created.');
        $this->redirectRoute('dashboards.index', navigate: true);
    }

    public function render(): View
    {
        $dashboards = DashboardModel::query()
            ->select(['id', 'name', 'owner_id', 'is_default', 'is_public', 'updated_at'])
            ->with(['owner:id,full_name'])
            ->when(
                $this->search !== '',
                fn (Builder $query) => $query->where('name', 'like', '%'.$this->search.'%')
            )
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('reports::livewire.dashboard-index', [
            'dashboards' => $dashboards,
        ])->extends('core::layouts.module', ['title' => 'Dashboards']);
    }
}
