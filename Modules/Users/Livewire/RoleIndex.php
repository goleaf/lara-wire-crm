<?php

namespace Modules\Users\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Users\Models\Role;

class RoleIndex extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('users.view'), 403);
    }

    public function updatePermission(string $roleId, string $permission, bool $value): void
    {
        abort_unless(auth()->user()?->can('users.edit'), 403);

        $allowed = ['can_view', 'can_create', 'can_edit', 'can_delete', 'can_export'];

        if (! in_array($permission, $allowed, true)) {
            return;
        }

        Role::query()->whereKey($roleId)->update([$permission => $value]);
        session()->flash('status', 'Role permission updated.');
    }

    public function render(): View
    {
        return view('users::livewire.role-index', [
            'roles' => Role::query()
                ->select([
                    'id',
                    'name',
                    'can_view',
                    'can_create',
                    'can_edit',
                    'can_delete',
                    'can_export',
                    'record_visibility',
                ])
                ->orderBy('name')
                ->get(),
        ])->extends('core::layouts.module', ['title' => 'Roles']);
    }
}
