<?php

namespace Modules\Cases\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Modules\Cases\Models\SupportCase;
use Modules\Notifications\Services\NotificationService;

class CheckSlaCases extends Command
{
    protected $signature = 'cases:check-sla';

    protected $description = 'Detect SLA-breached support cases and notify owners.';

    public function handle(): int
    {
        $supportsNotifications = class_exists(NotificationService::class);

        $cases = SupportCase::query()
            ->select(['id', 'number', 'title', 'owner_id', 'status', 'sla_deadline'])
            ->with(['owner:id,full_name,email,team_id,user_notification_preferences', 'owner.team:id,manager_id', 'owner.team.manager:id,full_name,email,user_notification_preferences'])
            ->whereNotIn('status', ['Resolved', 'Closed'])
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', now())
            ->get();

        $count = 0;

        foreach ($cases as $supportCase) {
            $count++;

            if (! $supportsNotifications) {
                continue;
            }

            $notificationService = app(NotificationService::class);

            if ($supportCase->owner) {
                $notificationService->send(
                    $supportCase->owner,
                    'SLA Breach',
                    'Case SLA breached',
                    "Case {$supportCase->number} is past SLA deadline.",
                    SupportCase::class,
                    (string) $supportCase->id,
                    Route::has('cases.show') ? route('cases.show', $supportCase->id) : null
                );
            }

            $manager = $supportCase->owner?->team?->manager;

            if ($manager && $manager->isNot($supportCase->owner)) {
                $notificationService->send(
                    $manager,
                    'SLA Breach',
                    'Team case SLA breached',
                    "Case {$supportCase->number} assigned to {$supportCase->owner?->full_name} breached SLA.",
                    SupportCase::class,
                    (string) $supportCase->id,
                    Route::has('cases.show') ? route('cases.show', $supportCase->id) : null
                );
            }
        }

        $this->info("SLA-breached cases found: {$count}");

        return self::SUCCESS;
    }
}
