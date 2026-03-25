<?php

namespace Modules\Users\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Users\Models\Team;

class TeamIndex extends Component
{
    use WithPagination;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.view'), 403);
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->user()?->can('users.delete'), 403);

        Team::query()->whereKey($id)->delete();
        session()->flash('status', 'Team deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('users::livewire.team-index', [
            'teams' => Team::query()
                ->select(['id', 'name', 'manager_id', 'region'])
                ->with(['manager:id,full_name'])
                ->withCount('members')
                ->orderBy('name')
                ->paginate(10),
        ])->extends('core::layouts.module', ['title' => 'Teams']);
    }
}
