<?php

namespace Modules\Reports\Metrics;

use Illuminate\Support\Collection;
use Modules\Cases\Models\SupportCase;

class CasesMetrics
{
    public function openCount(): int
    {
        return SupportCase::query()->open()->count();
    }

    public function avgResolutionHours(): float
    {
        $resolved = SupportCase::query()
            ->select(['id', 'created_at', 'resolved_at'])
            ->whereNotNull('resolved_at')
            ->get();

        return round((float) $resolved->avg(
            fn (SupportCase $supportCase): float => (float) $supportCase->created_at?->diffInMinutes($supportCase->resolved_at) / 60
        ), 2);
    }

    public function slaBreachRate(): float
    {
        $totalOpen = SupportCase::query()->open()->count();

        if ($totalOpen === 0) {
            return 0.0;
        }

        $breached = SupportCase::query()->overdue()->count();

        return round(($breached / $totalOpen) * 100, 2);
    }

    public function csatAverage(): float
    {
        return round((float) SupportCase::query()
            ->whereNotNull('satisfaction_score')
            ->avg('satisfaction_score'), 2);
    }

    /**
     * @return Collection<string, int>
     */
    public function casesByPriority(): Collection
    {
        return SupportCase::query()
            ->select(['id', 'priority'])
            ->get()
            ->countBy('priority');
    }

    /**
     * @return Collection<string, int>
     */
    public function casesByStatus(): Collection
    {
        return SupportCase::query()
            ->select(['id', 'status'])
            ->get()
            ->countBy('status');
    }
}
