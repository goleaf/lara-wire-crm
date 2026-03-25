<?php

namespace Modules\Calendar\Observers;

use Modules\Calendar\Models\CalendarEvent;
use Modules\Notifications\Models\CrmNotification;

class CalendarEventObserver
{
    public function creating(CalendarEvent $event): void
    {
        $this->queueReminderNotification($event);
    }

    public function updating(CalendarEvent $event): void
    {
        if ($event->isDirty(['reminder_minutes', 'start_at', 'organizer_id'])) {
            $this->queueReminderNotification($event);
        }
    }

    protected function queueReminderNotification(CalendarEvent $event): void
    {
        if (! $event->reminder_minutes || ! class_exists(CrmNotification::class)) {
            return;
        }

        $reminderAt = $event->start_at?->copy()->subMinutes($event->reminder_minutes);

        if (! $reminderAt) {
            return;
        }

        CrmNotification::query()->create([
            'user_id' => $event->organizer_id,
            'type' => 'Reminder',
            'title' => 'Calendar reminder',
            'body' => sprintf('"%s" starts at %s', $event->title, $event->start_at?->format('Y-m-d H:i')),
            'is_read' => false,
            'related_to_type' => CalendarEvent::class,
            'related_to_id' => $event->id,
            'action_url' => route('calendar.index', ['date' => $event->start_at?->toDateString()]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
