<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Notifications\Livewire\NotificationPreferences;
use Modules\Notifications\Models\CrmNotification;
use Modules\Notifications\Services\NotificationService;
use Modules\Users\Models\Role;

function makeNotificationsRole(): Role
{
    return Role::query()->create([
        'name' => 'Notifications Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['notifications' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open notification center', function () {
    $role = makeNotificationsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notification Center');
});

test('notifications service creates notification and mark read route marks it read', function () {
    $role = makeNotificationsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    app(NotificationService::class)->send(
        $user,
        'Reminder',
        'Follow up needed',
        'Call customer tomorrow.'
    );

    $notification = CrmNotification::query()
        ->forUser((string) $user->id)
        ->latest()
        ->firstOrFail();

    $this->actingAs($user)
        ->patch(route('notifications.read', $notification->id))
        ->assertRedirect(route('notifications.index'));

    expect($notification->fresh()->is_read)->toBeTrue();
});

test('users can update notification preferences', function () {
    $role = makeNotificationsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(NotificationPreferences::class)
        ->set('types.Reminder', false)
        ->set('quiet_hours_start', '22:00')
        ->set('quiet_hours_end', '07:00')
        ->call('save');

    $preferences = $user->fresh()->user_notification_preferences;

    expect($preferences['types']['Reminder'])->toBeFalse();
    expect($preferences['quiet_hours_start'])->toBe('22:00');
    expect($preferences['quiet_hours_end'])->toBe('07:00');
});
