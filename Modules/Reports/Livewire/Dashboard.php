<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Cases\Models\SupportCase;
use Modules\Deals\Models\Deal;
use Modules\Reports\Metrics\ActivitiesMetrics;
use Modules\Reports\Metrics\DealsMetrics;
use Modules\Reports\Metrics\InvoicesMetrics;
use Modules\Reports\Metrics\LeadsMetrics;
use Modules\Reports\Models\Dashboard as DashboardModel;

class Dashboard extends Component
{
    public bool $editMode = false;

    public function toggleEditMode(): void
    {
        $this->editMode = ! $this->editMode;
    }

    public function render(): View
    {
        $dashboard = DashboardModel::query()
            ->select(['id', 'name', 'owner_id', 'is_default', 'is_public', 'layout'])
            ->with([
                'widgets:id,dashboard_id,report_id,widget_type,title,position_x,position_y,width,height,config',
                'widgets.report:id,name,type,module',
            ])
            ->where(function ($query): void {
                $query
                    ->where('owner_id', auth()->id())
                    ->orWhere('is_default', true)
                    ->orWhere('is_public', true);
            })
            ->orderByDesc('owner_id')
            ->orderByDesc('is_default')
            ->first();

        $dealsMetrics = new DealsMetrics;
        $activitiesMetrics = new ActivitiesMetrics;
        $invoicesMetrics = new InvoicesMetrics;
        $leadsMetrics = new LeadsMetrics;

        $kpis = [
            'pipeline_value' => $dealsMetrics->pipelineValue(),
            'won_this_month' => $dealsMetrics->wonRevenue(),
            'open_cases' => class_exists(SupportCase::class) ? SupportCase::query()->open()->count() : 0,
            'overdue_activities' => $activitiesMetrics->overdueCount(),
            'outstanding_invoices' => $invoicesMetrics->outstanding(),
            'active_leads' => $leadsMetrics->totalLeads(),
        ];

        $recentDeals = class_exists(Deal::class)
            ? Deal::query()
                ->select(['id', 'name', 'amount', 'currency', 'stage_id', 'created_at'])
                ->with(['stage:id,name'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        $openCases = class_exists(SupportCase::class)
            ? SupportCase::query()
                ->select(['id', 'number', 'title', 'priority', 'status', 'created_at'])
                ->open()
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        $recentActivities = class_exists(Activity::class)
            ? Activity::query()
                ->select(['id', 'subject', 'type', 'status', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        return view('reports::livewire.dashboard', [
            'activeDashboard' => $dashboard,
            'openCases' => $openCases,
            'kpis' => $kpis,
            'pipelineStages' => $dealsMetrics->dealsByStage(),
            'recentActivities' => $recentActivities,
            'recentDeals' => $recentDeals,
            'widgets' => $dashboard?->widgets ?? collect(),
        ])->extends('core::layouts.module', ['title' => 'Dashboard']);
    }
}
