<?php

namespace Modules\Users\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Users\Models\Role;
use Nwidart\Modules\Facades\Module;

class RoleForm extends Component
{
    public ?string $roleId = null;

    public string $name = '';

    public bool $can_view = true;

    public bool $can_create = true;

    public bool $can_edit = true;

    public bool $can_delete = true;

    public bool $can_export = true;

    public string $record_visibility = 'own';

    /**
     * @var array<string, bool>
     */
    public array $module_access = [];

    public function mount(?string $id = null): void
    {
        $this->roleId = $id;
        $this->module_access = $this->defaultModuleAccess();

        if ($this->roleId) {
            abort_unless(auth()->user()?->can('users.edit'), 403);
            $this->loadRole();
        } else {
            abort_unless(auth()->user()?->can('users.create'), 403);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'can_view' => ['required', 'boolean'],
            'can_create' => ['required', 'boolean'],
            'can_edit' => ['required', 'boolean'],
            'can_delete' => ['required', 'boolean'],
            'can_export' => ['required', 'boolean'],
            'record_visibility' => ['required', 'in:own,team,all'],
            'module_access' => ['required', 'array'],
        ]);

        Role::query()->updateOrCreate(
            ['id' => $this->roleId],
            $validated
        );

        session()->flash('status', $this->roleId ? 'Role updated.' : 'Role created.');
        $this->redirectRoute('roles.index', navigate: true);
    }

    protected function loadRole(): void
    {
        $role = Role::query()->findOrFail($this->roleId);

        $this->name = $role->name;
        $this->can_view = (bool) $role->can_view;
        $this->can_create = (bool) $role->can_create;
        $this->can_edit = (bool) $role->can_edit;
        $this->can_delete = (bool) $role->can_delete;
        $this->can_export = (bool) $role->can_export;
        $this->record_visibility = $role->record_visibility;

        foreach ($this->module_access as $module => $enabled) {
            $this->module_access[$module] = (bool) ($role->module_access[$module] ?? $enabled);
        }
    }

    /**
     * @return array<string, bool>
     */
    protected function defaultModuleAccess(): array
    {
        return Module::allEnabled()
            ->mapWithKeys(fn ($module): array => [strtolower($module->getName()) => true])
            ->all();
    }

    public function render(): View
    {
        return view('users::livewire.role-form', [
            'moduleAccessOptions' => array_keys($this->module_access),
        ])->extends('core::layouts.module', [
            'title' => $this->roleId ? 'Edit Role' : 'Create Role',
        ]);
    }
}
