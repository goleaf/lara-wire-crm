<?php

namespace Modules\Reports\Metrics;

use Modules\Campaigns\Models\Campaign;

class CampaignsMetrics
{
    public function totalLeads(): int
    {
        return Campaign::query()->withCount('leads')->get()->sum('leads_count');
    }

    public function roi(): float
    {
        $campaigns = Campaign::query()
            ->select(['id', 'actual_cost'])
            ->get();

        if ($campaigns->isEmpty()) {
            return 0.0;
        }

        return round((float) $campaigns->avg(fn (Campaign $campaign): float => (float) $campaign->roi), 2);
    }

    public function conversionRate(): float
    {
        $campaigns = Campaign::query()->select(['id'])->get();

        if ($campaigns->isEmpty()) {
            return 0.0;
        }

        return round((float) $campaigns->avg(fn (Campaign $campaign): float => (float) $campaign->lead_conversion_rate), 2);
    }

    public function budgetVsActual(): array
    {
        $budget = (float) Campaign::query()->sum('budget');
        $actual = (float) Campaign::query()->sum('actual_cost');

        return [
            'budget' => $budget,
            'actual' => $actual,
            'variance' => $budget - $actual,
        ];
    }
}
