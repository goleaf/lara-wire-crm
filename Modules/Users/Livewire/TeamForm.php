<?php

namespace Modules\Users\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Users\Models\Team;

class TeamForm extends Component
{
    public ?string $teamId = null;

    public string $name = '';

    public string $manager_id = '';

    public string $region = '';

    /**
     * @var array<int, string>
     */
    public array $member_ids = [];

    public function mount(?string $id = null): void
    {
        $this->teamId = $id;

        if ($this->teamId) {
            abort_unless(auth()->user()?->can('users.edit'), 403);
            $this->loadTeam();
        } else {
            abort_unless(auth()->user()?->can('users.create'), 403);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'region' => ['nullable', 'string', 'max:255'],
            'member_ids' => ['array'],
            'member_ids.*' => ['exists:users,id'],
        ]);

        $team = Team::query()->updateOrCreate(
            ['id' => $this->teamId],
            [
                'name' => $validated['name'],
                'manager_id' => $validated['manager_id'] !== '' ? $validated['manager_id'] : null,
                'region' => $validated['region'],
            ]
        );

        $team->members()->sync($this->member_ids);

        session()->flash('status', $this->teamId ? 'Team updated.' : 'Team created.');
        $this->redirectRoute('teams.index', navigate: true);
    }

    protected function loadTeam(): void
    {
        $team = Team::query()->with('members:id')->findOrFail($this->teamId);

        $this->name = $team->name;
        $this->manager_id = (string) ($team->manager_id ?? '');
        $this->region = (string) ($team->region ?? '');
        $this->member_ids = $team->members->pluck('id')->all();
    }

    public function render(): View
    {
        return view('users::livewire.team-form', [
            'users' => User::query()
                ->select(['id', 'full_name', 'email'])
                ->orderBy('full_name')
                ->get(),
        ])->extends('core::layouts.module', [
            'title' => $this->teamId ? 'Edit Team' : 'Create Team',
        ]);
    }
}
