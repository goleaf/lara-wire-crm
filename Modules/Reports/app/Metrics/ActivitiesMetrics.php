<?php

namespace Modules\Reports\Metrics;

use Illuminate\Support\Collection;
use Modules\Activities\Models\Activity;

class ActivitiesMetrics
{
    public function completedToday(): int
    {
        return Activity::query()
            ->where('status', 'Completed')
            ->whereDate('completed_at', now()->toDateString())
            ->count();
    }

    public function overdueCount(): int
    {
        return Activity::query()->overdue()->count();
    }

    /**
     * @return Collection<string, int>
     */
    public function byType(): Collection
    {
        return Activity::query()
            ->select(['id', 'type'])
            ->get()
            ->countBy('type');
    }

    /**
     * @return Collection<string, int>
     */
    public function byOwner(): Collection
    {
        return Activity::query()
            ->select(['id', 'owner_id'])
            ->with(['owner:id,full_name'])
            ->get()
            ->countBy(fn (Activity $activity): string => (string) ($activity->owner?->full_name ?? 'Unassigned'));
    }

    public function completionRate(): float
    {
        $total = Activity::query()->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = Activity::query()->where('status', 'Completed')->count();

        return round(($completed / $total) * 100, 2);
    }
}
