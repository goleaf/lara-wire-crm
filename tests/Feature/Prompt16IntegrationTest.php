<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Users\Livewire\Profile;
use Modules\Users\Models\Role;

function makePrompt16Role(): Role
{
    return Role::query()->create([
        'name' => 'Admin',
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'core' => true,
            'users' => true,
            'reports' => true,
            'contacts' => true,
            'deals' => true,
            'leads' => true,
            'campaigns' => true,
            'cases' => true,
            'notifications' => true,
            'activities' => true,
        ],
    ]);
}

test('authorized users can open prompt 16 core pages', function () {
    $role = makePrompt16Role();
    $user = User::factory()->create([
        'role_id' => $role->id,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('core.settings'))
        ->assertOk()
        ->assertSee('Settings');

    $this->actingAs($user)
        ->get(route('core.audit-logs'))
        ->assertOk()
        ->assertSee('Audit Logs');

    $this->actingAs($user)
        ->get(route('users.profile'))
        ->assertOk()
        ->assertSee('My Profile');
});

test('profile page updates account and notification preferences', function () {
    $role = makePrompt16Role();
    $user = User::factory()->create([
        'role_id' => $role->id,
        'is_active' => true,
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('full_name', 'Prompt Sixteen User')
        ->set('email', 'prompt16@example.test')
        ->set('notification_types.Reminder', false)
        ->set('quiet_hours_start', '22:00')
        ->set('quiet_hours_end', '07:00')
        ->call('save')
        ->assertHasNoErrors();

    $freshUser = $user->fresh();

    expect($freshUser?->full_name)->toBe('Prompt Sixteen User');
    expect($freshUser?->email)->toBe('prompt16@example.test');
    expect(data_get($freshUser?->user_notification_preferences, 'types.Reminder'))->toBeFalse();
    expect(data_get($freshUser?->user_notification_preferences, 'quiet_hours_start'))->toBe('22:00');
    expect(data_get($freshUser?->user_notification_preferences, 'quiet_hours_end'))->toBe('07:00');
});

test('crm install command can run in no-op mode', function () {
    $this->artisan('crm:install', [
        '--skip-migrate' => true,
        '--skip-seed' => true,
    ])->assertExitCode(0);
});
