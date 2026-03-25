<?php

namespace Modules\Activities\Observers;

use Modules\Activities\Models\Activity;

class ActivityObserver
{
    public function updating(Activity $activity): void
    {
        if ($activity->isDirty('status') && $activity->status === 'Completed') {
            $activity->completed_at = now();
        }
    }

    public function creating(Activity $activity): void
    {
        if ($activity->reminder_at) {
            $activity->reminder_sent = false;
        }
    }
}
