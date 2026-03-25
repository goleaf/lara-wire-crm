<?php

namespace Modules\Deals\Observers;

use Modules\Deals\Models\Deal;
use Modules\Deals\Models\PipelineStage;

class DealObserver
{
    public function saving(Deal $deal): void
    {
        if ($deal->isDirty(['amount', 'probability'])) {
            $deal->expected_revenue = round(((float) $deal->amount * (int) $deal->probability) / 100, 2);
        }
    }

    public function updating(Deal $deal): void
    {
        if (! $deal->isDirty('stage_id')) {
            return;
        }

        $stage = PipelineStage::query()
            ->select(['id', 'name', 'probability'])
            ->find($deal->stage_id);

        if (! $stage) {
            return;
        }

        $deal->probability = $stage->probability;
        $deal->expected_revenue = round(((float) $deal->amount * (int) $stage->probability) / 100, 2);

        if (in_array($stage->name, ['Closed Won', 'Closed Lost'], true)) {
            $deal->closed_at = now();
        }
    }
}
