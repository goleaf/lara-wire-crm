<?php

use App\Models\User;
use Carbon\CarbonImmutable;
use Livewire\Livewire;
use Modules\Calendar\Livewire\EventForm;
use Modules\Calendar\Models\CalendarEvent;
use Modules\Users\Models\Role;

function makeCalendarRole(): Role
{
    return Role::query()->create([
        'name' => 'Calendar Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['calendar' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open calendar page', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('calendar.index'))
        ->assertOk()
        ->assertSee('Calendar');
});

test('event form creates calendar event', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(EventForm::class)
        ->set('title', 'Demo call')
        ->set('type', 'Demo')
        ->set('startAt', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('endAt', now()->addDay()->addHour()->format('Y-m-d\TH:i'))
        ->set('organizerId', $user->id)
        ->set('status', 'Scheduled')
        ->call('save');

    $this->assertDatabaseHas('calendar_events', [
        'title' => 'Demo call',
        'organizer_id' => $user->id,
        'type' => 'Demo',
    ]);
});

test('calendar events endpoint returns matching range', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $event = CalendarEvent::query()->create([
        'title' => 'Weekly planning',
        'type' => 'Meeting',
        'start_at' => now()->addDay()->setTime(11, 0),
        'end_at' => now()->addDay()->setTime(12, 0),
        'organizer_id' => $user->id,
        'status' => 'Scheduled',
        'recurrence' => 'None',
    ]);

    $this->actingAs($user)
        ->get(route('calendar.events', [
            'from' => now()->toDateString(),
            'to' => now()->addDays(7)->toDateString(),
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'id' => $event->id,
            'title' => 'Weekly planning',
        ]);
});

test('calendar recurring events handle immutable carbon dates safely', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $event = CalendarEvent::query()->create([
        'title' => 'Immutable recurrence',
        'type' => 'Meeting',
        'start_at' => CarbonImmutable::now()->addDay()->setTime(9, 0),
        'end_at' => CarbonImmutable::now()->addDay()->setTime(10, 0),
        'organizer_id' => $user->id,
        'status' => 'Scheduled',
        'recurrence' => 'Daily',
        'recurrence_end_date' => CarbonImmutable::now()->addDays(3)->toDateString(),
    ]);

    $occurrences = $event->generateRecurrences();

    expect($occurrences)->not->toBeEmpty();
    expect($occurrences->count())->toBeGreaterThan(1);
});
