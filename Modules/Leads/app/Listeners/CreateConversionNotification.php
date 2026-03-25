<?php

namespace Modules\Leads\Listeners;

use Modules\Leads\Events\LeadConverted;
use Modules\Leads\Models\Lead;
use Modules\Notifications\Models\CrmNotification;
use Modules\Notifications\Services\NotificationService;

class CreateConversionNotification
{
    public function handle(LeadConverted $event): void
    {
        $owner = $event->lead->owner;

        if (! $owner) {
            return;
        }

        if (class_exists(NotificationService::class)) {
            app(NotificationService::class)->send(
                $owner,
                'Assignment',
                'Lead converted',
                $event->lead->full_name.' was converted successfully.',
                Lead::class,
                $event->lead->id,
                route('leads.show', $event->lead->id),
            );

            return;
        }

        if (class_exists(CrmNotification::class)) {
            CrmNotification::notify(
                $owner,
                'Assignment',
                'Lead converted',
                [
                    'body' => $event->lead->full_name.' was converted successfully.',
                    'related_to_type' => Lead::class,
                    'related_to_id' => $event->lead->id,
                    'action_url' => route('leads.show', $event->lead->id),
                ],
            );
        }
    }
}
