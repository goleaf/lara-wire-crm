<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Activities\Livewire\ActivityDetail;
use Modules\Activities\Livewire\ActivityForm;
use Modules\Activities\Models\Activity;
use Modules\Users\Models\Role;

function makeActivitiesRole(): Role
{
    return Role::query()->create([
        'name' => 'Activities Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['activities' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open activities feed', function () {
    $role = makeActivitiesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('activities.index'))
        ->assertOk()
        ->assertSee('Activity Feed');
});

test('users can create activity from livewire form', function () {
    $role = makeActivitiesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ActivityForm::class)
        ->set('type', 'Task')
        ->set('subject', 'Follow up call')
        ->set('status', 'Planned')
        ->set('priority', 'High')
        ->set('dueDate', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('ownerId', $user->id)
        ->set('attendeeIds', [$user->id])
        ->call('save');

    $this->assertDatabaseHas('activities', [
        'subject' => 'Follow up call',
        'owner_id' => $user->id,
        'type' => 'Task',
    ]);
});

test('livewire complete action marks activity completed', function () {
    $role = makeActivitiesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $activity = Activity::query()->create([
        'type' => 'Meeting',
        'subject' => 'Quarterly sync',
        'status' => 'Planned',
        'priority' => 'Normal',
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ActivityDetail::class, ['id' => $activity->id])
        ->call('markComplete');

    $activity->refresh();

    expect($activity->status)->toBe('Completed');
    expect($activity->completed_at)->not->toBeNull();
});

test('livewire delete action removes activity and redirects', function () {
    $role = makeActivitiesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $activity = Activity::query()->create([
        'type' => 'Task',
        'subject' => 'Delete me',
        'status' => 'Planned',
        'priority' => 'Normal',
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ActivityDetail::class, ['id' => $activity->id])
        ->call('delete')
        ->assertRedirectToRoute('activities.index');

    $this->assertDatabaseMissing('activities', [
        'id' => $activity->id,
    ]);
});
