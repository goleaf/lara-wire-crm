<?php

namespace Modules\Activities\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Activities\Models\Activity;

class MyActivities extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('activities.view'), 403);
    }

    public function markDone(string $id, bool $value = true): void
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        if (! $value) {
            return;
        }

        Activity::query()
            ->whereKey($id)
            ->where('owner_id', auth()->id())
            ->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
    }

    public function render(): View
    {
        $baseQuery = Activity::query()
            ->select([
                'id',
                'type',
                'subject',
                'status',
                'priority',
                'due_date',
                'related_to_type',
                'related_to_id',
                'owner_id',
            ])
            ->where('owner_id', auth()->id())
            ->where('status', 'Planned')
            ->orderBy('due_date');

        $overdue = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->limit(20)
            ->get();

        $today = (clone $baseQuery)
            ->whereDate('due_date', today())
            ->limit(20)
            ->get();

        $upcoming = (clone $baseQuery)
            ->whereBetween('due_date', [now()->addDay()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->limit(20)
            ->get();

        return view('activities::livewire.my-activities', [
            'overdue' => $overdue,
            'today' => $today,
            'upcoming' => $upcoming,
        ])->extends('core::layouts.module', ['title' => 'My Activities']);
    }
}
