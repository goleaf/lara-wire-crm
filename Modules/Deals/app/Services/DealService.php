<?php

namespace Modules\Deals\Services;

use Modules\Deals\Models\Deal;
use Modules\Deals\Models\PipelineStage;

class DealService
{
    public function moveToStage(Deal $deal, PipelineStage $stage): void
    {
        $deal->forceFill([
            'stage_id' => $stage->id,
            'pipeline_id' => $stage->pipeline_id,
        ])->save();
    }

    public function markWon(Deal $deal): void
    {
        $wonStage = PipelineStage::query()
            ->where('pipeline_id', $deal->pipeline_id)
            ->where('name', 'Closed Won')
            ->firstOrFail();

        $deal->forceFill([
            'stage_id' => $wonStage->id,
            'closed_at' => now(),
            'lost_reason' => null,
            'lost_notes' => null,
        ])->save();
    }

    public function markLost(Deal $deal, string $reason, ?string $notes): void
    {
        $lostStage = PipelineStage::query()
            ->where('pipeline_id', $deal->pipeline_id)
            ->where('name', 'Closed Lost')
            ->firstOrFail();

        $deal->forceFill([
            'stage_id' => $lostStage->id,
            'lost_reason' => $reason,
            'lost_notes' => filled($notes) ? $notes : null,
            'closed_at' => now(),
        ])->save();
    }

    /**
     * @return array{subtotal:float,products_total:float}
     */
    public function calculateTotals(Deal $deal): array
    {
        $productsTotal = (float) $deal->products()->sum('deal_products.total');

        return [
            'subtotal' => (float) $deal->amount,
            'products_total' => $productsTotal,
        ];
    }
}
