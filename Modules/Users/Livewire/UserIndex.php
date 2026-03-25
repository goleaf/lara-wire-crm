<?php

namespace Modules\Users\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Users\Models\Role;
use Modules\Users\Models\Team;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public string $teamFilter = '';

    public string $activeFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTeamFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function toggleActive(string $id): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403);

        $user = User::query()->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        session()->flash('status', 'User status updated.');
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->user()?->can('users.delete'), 403);

        if (auth()->id() === $id) {
            session()->flash('status', 'You cannot delete your own account.');

            return;
        }

        User::query()->whereKey($id)->delete();
        session()->flash('status', 'User deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $users = User::query()
            ->select([
                'id',
                'full_name',
                'email',
                'role_id',
                'team_id',
                'is_active',
                'last_login',
                'avatar_path',
            ])
            ->with([
                'role:id,name',
                'team:id,name',
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner
                        ->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter !== '', fn ($query) => $query->where('role_id', $this->roleFilter))
            ->when($this->teamFilter !== '', fn ($query) => $query->where('team_id', $this->teamFilter))
            ->when($this->activeFilter !== '', fn ($query) => $query->where('is_active', $this->activeFilter === '1'))
            ->orderBy('full_name')
            ->paginate(10);

        return view('users::livewire.user-index', [
            'roles' => Role::query()->select(['id', 'name'])->orderBy('name')->get(),
            'teams' => Team::query()->select(['id', 'name'])->orderBy('name')->get(),
            'users' => $users,
        ])->extends('core::layouts.module', ['title' => 'Users']);
    }
}
