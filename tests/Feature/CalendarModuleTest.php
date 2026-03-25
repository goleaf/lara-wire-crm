<?php

use App\Models\User;
use Carbon\CarbonImmutable;
use Livewire\Livewire;
use Modules\Calendar\Livewire\CalendarView;
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

    $this->actingAs($user);

    Livewire::test(CalendarView::class)
        ->assertSet('view', 'month')
        ->assertViewHas('rangeFrom')
        ->assertViewHas('rangeTo');
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

test('calendar view exposes range events for the visible period', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $eventInRange = CalendarEvent::query()->create([
        'title' => 'Weekly planning',
        'type' => 'Meeting',
        'start_at' => now()->addDay()->setTime(11, 0),
        'end_at' => now()->addDay()->setTime(12, 0),
        'organizer_id' => $user->id,
        'status' => 'Scheduled',
        'recurrence' => 'None',
    ]);

    $eventOutOfRange = CalendarEvent::query()->create([
        'title' => 'Far roadmap sync',
        'type' => 'Meeting',
        'start_at' => now()->addMonths(2)->setTime(11, 0),
        'end_at' => now()->addMonths(2)->setTime(12, 0),
        'organizer_id' => $user->id,
        'status' => 'Scheduled',
        'recurrence' => 'None',
    ]);

    $this->actingAs($user);

    Livewire::test(CalendarView::class, [
        'view' => 'week',
        'date' => now()->toDateString(),
    ])->assertViewHas('rangeEvents', function (array $rangeEvents) use ($eventInRange, $eventOutOfRange): bool {
        $eventIds = collect($rangeEvents)->pluck('id');

        return $eventIds->contains($eventInRange->id) && ! $eventIds->contains($eventOutOfRange->id);
    });
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

test('calendar month view shows event titles for visible days', function () {
    $role = makeCalendarRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    CalendarEvent::query()->create([
        'title' => 'Quarterly planning review',
        'type' => 'Meeting',
        'start_at' => now()->addDay()->setTime(14, 0),
        'end_at' => now()->addDay()->setTime(15, 0),
        'organizer_id' => $user->id,
        'status' => 'Scheduled',
        'recurrence' => 'None',
        'color' => '#0ea5e9',
    ]);

    $this->actingAs($user);

    Livewire::test(CalendarView::class, [
        'view' => 'month',
        'date' => now()->toDateString(),
    ])->assertViewHas('eventsByDate', function ($eventsByDate): bool {
        return $eventsByDate->flatten()->contains(fn ($event) => $event->title === 'Quarterly planning review');
    });
});
