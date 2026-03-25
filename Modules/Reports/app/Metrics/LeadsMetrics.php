<?php

namespace Modules\Reports\Metrics;

use Illuminate\Support\Collection;
use Modules\Leads\Models\Lead;

class LeadsMetrics
{
    public function totalLeads(): int
    {
        return Lead::query()->count();
    }

    public function conversionRate(): float
    {
        $total = Lead::query()->count();

        if ($total === 0) {
            return 0.0;
        }

        $converted = Lead::query()->where('converted', true)->count();

        return round(($converted / $total) * 100, 2);
    }

    /**
     * @return Collection<string, int>
     */
    public function leadsBySource(): Collection
    {
        return Lead::query()
            ->select(['id', 'lead_source'])
            ->get()
            ->countBy('lead_source');
    }

    /**
     * @return Collection<string, int>
     */
    public function leadsByStatus(): Collection
    {
        return Lead::query()
            ->select(['id', 'status'])
            ->get()
            ->countBy('status');
    }

    public function avgScore(): float
    {
        return round((float) Lead::query()->avg('score'), 2);
    }

    public function leadsThisMonth(): int
    {
        return Lead::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }
}
