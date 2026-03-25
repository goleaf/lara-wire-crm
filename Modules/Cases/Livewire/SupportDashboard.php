<?php

namespace Modules\Cases\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Cases\Models\SupportCase;

class SupportDashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('cases.view'), 403);
    }

    public function render(): View
    {
        $openCasesCount = SupportCase::query()->open()->count();
        $overdueCasesCount = SupportCase::query()->overdue()->count();

        $resolvedCases = SupportCase::query()
            ->select(['id', 'created_at', 'resolved_at'])
            ->whereNotNull('resolved_at')
            ->get();

        $averageResolutionHours = round((float) $resolvedCases->avg(
            fn (SupportCase $supportCase): float => (float) $supportCase->created_at?->diffInMinutes($supportCase->resolved_at) / 60
        ), 2);

        $averageCsat = round((float) SupportCase::query()
            ->whereNotNull('satisfaction_score')
            ->avg('satisfaction_score'), 2);

        $priorityCounts = SupportCase::query()
            ->select(['id', 'priority'])
            ->get()
            ->countBy('priority');

        $priorities = [
            ['name' => 'Critical', 'color' => 'bg-rose-500', 'count' => (int) $priorityCounts->get('Critical', 0)],
            ['name' => 'High', 'color' => 'bg-orange-500', 'count' => (int) $priorityCounts->get('High', 0)],
            ['name' => 'Medium', 'color' => 'bg-amber-500', 'count' => (int) $priorityCounts->get('Medium', 0)],
            ['name' => 'Low', 'color' => 'bg-slate-400', 'count' => (int) $priorityCounts->get('Low', 0)],
        ];

        $myOpenCases = SupportCase::query()
            ->select(['id', 'number', 'title', 'priority', 'status', 'sla_deadline', 'owner_id'])
            ->with(['owner:id,full_name'])
            ->where('owner_id', auth()->id())
            ->open()
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $slaBreaches = SupportCase::query()
            ->select(['id', 'number', 'title', 'priority', 'owner_id', 'sla_deadline'])
            ->with(['owner:id,full_name'])
            ->overdue()
            ->orderBy('sla_deadline')
            ->limit(8)
            ->get();

        return view('cases::livewire.support-dashboard', [
            'averageCsat' => $averageCsat,
            'averageResolutionHours' => $averageResolutionHours,
            'myOpenCases' => $myOpenCases,
            'openCasesCount' => $openCasesCount,
            'overdueCasesCount' => $overdueCasesCount,
            'priorities' => $priorities,
            'slaBreaches' => $slaBreaches,
        ])->extends('core::layouts.module', ['title' => 'Support Dashboard']);
    }
}
