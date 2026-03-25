<?php

namespace Modules\Cases\Observers;

use Modules\Cases\Models\SupportCase;

class SupportCaseObserver
{
    public function creating(SupportCase $supportCase): void
    {
        if (blank($supportCase->number)) {
            $supportCase->number = $supportCase->generateNumber();
        }

        $supportCase->assignSla();
    }

    public function updating(SupportCase $supportCase): void
    {
        if (! $supportCase->isDirty('status')) {
            return;
        }

        if ($supportCase->status === 'Resolved' && $supportCase->resolved_at === null) {
            $supportCase->resolved_at = now();
        }

        if ($supportCase->status === 'Closed' && $supportCase->closed_at === null) {
            $supportCase->closed_at = now();
        }
    }
}
