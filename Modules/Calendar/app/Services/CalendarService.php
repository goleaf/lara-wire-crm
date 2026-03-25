<?php

namespace Modules\Calendar\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\Activities\Models\Activity;
use Modules\Calendar\Models\CalendarEvent;

class CalendarService
{
    public function getEventsForMonth(int $year, int $month, User $user): Collection
    {
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to = $from->copy()->endOfMonth();

        return $this->eventsWithRecurrences($from, $to, $user);
    }

    public function getEventsForWeek(CarbonInterface $weekStart, User $user): Collection
    {
        $from = Carbon::instance($weekStart)->startOfWeek(Carbon::MONDAY);
        $to = $from->copy()->endOfWeek(Carbon::SUNDAY);

        return $this->eventsWithRecurrences($from, $to, $user);
    }

    public function getEventsForDay(CarbonInterface $day, User $user): Collection
    {
        $from = Carbon::instance($day)->startOfDay();
        $to = $day->copy()->endOfDay();

        return $this->eventsWithRecurrences($from, $to, $user);
    }

    /**
     * @return array<int, CalendarEvent>
     */
    public function expandRecurring(CalendarEvent $event, CarbonInterface $from, CarbonInterface $to): array
    {
        $fromDate = Carbon::instance($from);
        $toDate = Carbon::instance($to);

        return $event->generateRecurrences()
            ->filter(fn (CalendarEvent $occurrence): bool => $occurrence->start_at->between($fromDate, $toDate))
            ->values()
            ->all();
    }

    public function createFromActivity(Activity $activity): CalendarEvent
    {
        return CalendarEvent::query()->create([
            'title' => $activity->subject,
            'type' => $activity->type === 'Meeting' ? 'Meeting' : 'Reminder',
            'start_at' => $activity->due_date ?? now(),
            'end_at' => $activity->due_date ? $activity->due_date->copy()->addMinutes($activity->duration_minutes ?? 30) : null,
            'all_day' => false,
            'description' => $activity->description,
            'organizer_id' => $activity->owner_id,
            'status' => $activity->status === 'Cancelled' ? 'Cancelled' : 'Scheduled',
            'recurrence' => 'None',
        ]);
    }

    protected function eventsWithRecurrences(CarbonInterface $from, CarbonInterface $to, User $user): Collection
    {
        $fromDate = Carbon::instance($from);
        $toDate = Carbon::instance($to);

        $baseEvents = CalendarEvent::query()
            ->select([
                'id',
                'title',
                'type',
                'start_at',
                'end_at',
                'all_day',
                'location',
                'description',
                'organizer_id',
                'contact_id',
                'deal_id',
                'reminder_minutes',
                'recurrence',
                'recurrence_end_date',
                'status',
                'color',
            ])
            ->forUser($user->id)
            ->where('recurrence', 'None')
            ->inRange($fromDate, $toDate)
            ->with(['organizer:id,full_name', 'attendees:id,full_name'])
            ->orderBy('start_at')
            ->get();

        $recurringEvents = CalendarEvent::query()
            ->select([
                'id',
                'title',
                'type',
                'start_at',
                'end_at',
                'all_day',
                'location',
                'description',
                'organizer_id',
                'contact_id',
                'deal_id',
                'reminder_minutes',
                'recurrence',
                'recurrence_end_date',
                'status',
                'color',
            ])
            ->forUser($user->id)
            ->where('recurrence', '!=', 'None')
            ->where('start_at', '<=', $toDate)
            ->where(function ($query) use ($fromDate): void {
                $query
                    ->whereNull('recurrence_end_date')
                    ->orWhereDate('recurrence_end_date', '>=', $fromDate->toDateString());
            })
            ->with(['organizer:id,full_name', 'attendees:id,full_name'])
            ->get()
            ->flatMap(fn (CalendarEvent $event): array => $this->expandRecurring($event, $fromDate, $toDate));

        return $baseEvents
            ->concat($recurringEvents)
            ->sortBy(fn (CalendarEvent $event) => $event->start_at)
            ->values();
    }
}
