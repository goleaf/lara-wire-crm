<?php

namespace Modules\Reports\Metrics;

use Illuminate\Support\Collection;
use Modules\Deals\Models\Deal;

class DealsMetrics
{
    public function totalRevenue(): float
    {
        return (float) Deal::query()->sum('amount');
    }

    public function wonRevenue(): float
    {
        return (float) Deal::query()
            ->whereHas('stage', fn ($query) => $query->where('name', 'Closed Won'))
            ->sum('amount');
    }

    public function avgDealSize(): float
    {
        return round((float) Deal::query()->avg('amount'), 2);
    }

    public function winRate(): float
    {
        $total = Deal::query()->count();

        if ($total === 0) {
            return 0.0;
        }

        $won = Deal::query()
            ->whereHas('stage', fn ($query) => $query->where('name', 'Closed Won'))
            ->count();

        return round(($won / $total) * 100, 2);
    }

    /**
     * @return Collection<string, int>
     */
    public function dealsByStage(): Collection
    {
        return Deal::query()
            ->select(['id', 'stage_id'])
            ->with(['stage:id,name'])
            ->get()
            ->countBy(fn (Deal $deal): string => (string) ($deal->stage?->name ?? 'Unassigned'));
    }

    /**
     * @return Collection<string, float>
     */
    public function revenueByMonth(): Collection
    {
        return Deal::query()
            ->select(['id', 'amount', 'created_at'])
            ->get()
            ->groupBy(fn (Deal $deal): string => $deal->created_at?->format('Y-m') ?? 'Unknown')
            ->map(fn (Collection $items): float => (float) $items->sum('amount'));
    }

    public function pipelineValue(): float
    {
        return (float) Deal::query()
            ->whereDoesntHave('stage', fn ($query) => $query->whereIn('name', ['Closed Won', 'Closed Lost']))
            ->sum('amount');
    }

    public function forecastedRevenue(): float
    {
        return (float) Deal::query()->sum('expected_revenue');
    }
}
