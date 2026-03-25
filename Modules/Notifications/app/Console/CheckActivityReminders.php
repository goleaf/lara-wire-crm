<?php

namespace Modules\Notifications\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Modules\Activities\Models\Activity;
use Modules\Notifications\Services\NotificationService;

class CheckActivityReminders extends Command
{
    protected $signature = 'notifications:check-activity-reminders';

    protected $description = 'Create in-app reminder notifications for upcoming activities.';

    public function handle(NotificationService $notificationService): int
    {
        $activityClass = Activity::class;

        if (! class_exists($activityClass) || ! Schema::hasTable('activities')) {
            $this->info('Activities module is not available. Nothing to process.');

            return self::SUCCESS;
        }

        $hasReminderSent = Schema::hasColumn('activities', 'reminder_sent');

        $activities = $activityClass::query()
            ->select(['id', 'subject', 'owner_id', 'related_to_type', 'related_to_id', 'due_date', 'reminder_at'])
            ->with('owner:id,full_name,email,user_notification_preferences')
            ->where('status', 'Planned')
            ->whereBetween('reminder_at', [now(), now()->copy()->addMinutes(15)])
            ->when($hasReminderSent, fn ($query) => $query->where('reminder_sent', false))
            ->get();

        $sent = 0;

        foreach ($activities as $activity) {
            if (! $activity->owner) {
                continue;
            }

            $notificationService->send(
                $activity->owner,
                'Reminder',
                'Upcoming activity reminder',
                'Activity: '.$activity->subject,
                (string) $activity->related_to_type,
                $activity->related_to_id ? (string) $activity->related_to_id : null,
                Route::has('activities.show') ? route('activities.show', $activity->id) : null
            );

            if ($hasReminderSent) {
                $activity->forceFill(['reminder_sent' => true])->save();
            }

            $sent++;
        }

        $this->info("Activity reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
