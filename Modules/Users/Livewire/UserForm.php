<?php

namespace Modules\Users\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Users\Models\Role;
use Modules\Users\Models\Team;

class UserForm extends Component
{
    use WithFileUploads;

    public ?string $userId = null;

    public string $full_name = '';

    public string $email = '';

    public string $password = '';

    public string $role_id = '';

    public string $team_id = '';

    public string $quota = '0.00';

    public bool $is_active = true;

    public ?TemporaryUploadedFile $avatar = null;

    public ?string $existingAvatarPath = null;

    public function mount(?string $id = null): void
    {
        $this->userId = $id;

        if ($this->userId) {
            abort_unless(auth()->user()?->can('users.edit'), 403);
            $this->loadUser($this->userId);
        } else {
            abort_unless(auth()->user()?->can('users.create'), 403);
        }
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $payload = [
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'team_id' => $validated['team_id'] !== '' ? $validated['team_id'] : null,
            'quota' => $validated['quota'],
            'is_active' => $validated['is_active'],
        ];

        if ($validated['password'] !== '') {
            $payload['password'] = Hash::make($validated['password']);
        }

        if ($this->avatar) {
            $payload['avatar_path'] = $this->avatar->store('avatars', 'public');
        }

        if ($this->userId) {
            User::query()->whereKey($this->userId)->update($payload);
            session()->flash('status', 'User updated successfully.');
        } else {
            User::query()->create($payload);
            session()->flash('status', 'User created successfully.');
        }

        $this->redirectRoute('users.index', navigate: true);
    }

    protected function loadUser(string $id): void
    {
        $user = User::query()->findOrFail($id);

        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->role_id = (string) $user->role_id;
        $this->team_id = (string) ($user->team_id ?? '');
        $this->quota = (string) $user->quota;
        $this->is_active = (bool) $user->is_active;
        $this->existingAvatarPath = $user->avatar_path;
    }

    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($this->userId),
            ],
            'password' => $this->userId
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'team_id' => ['nullable', Rule::exists('teams', 'id')],
            'quota' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function render(): View
    {
        return view('users::livewire.user-form', [
            'roles' => Role::query()->select(['id', 'name'])->orderBy('name')->get(),
            'teams' => Team::query()->select(['id', 'name'])->orderBy('name')->get(),
        ])->extends('core::layouts.module', [
            'title' => $this->userId ? 'Edit User' : 'Create User',
        ]);
    }
}
