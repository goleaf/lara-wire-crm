<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Reports\Models\Dashboard;
use Modules\Reports\Models\DashboardWidget;
use Modules\Reports\Models\Report;
use Modules\Reports\Services\ReportService;

class ReportView extends Component
{
    use WithPagination;

    public string $reportId = '';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);

        $this->reportId = $id;
    }

    public function addToDashboard(): void
    {
        abort_unless(auth()->user()?->can('reports.edit'), 403);

        $dashboard = Dashboard::query()->firstOrCreate(
            ['owner_id' => auth()->id(), 'is_default' => true],
            [
                'name' => 'Default Dashboard',
                'is_public' => false,
                'layout' => [],
            ]
        );

        $widgetCount = DashboardWidget::query()
            ->where('dashboard_id', $dashboard->id)
            ->count();

        DashboardWidget::query()->create([
            'dashboard_id' => $dashboard->id,
            'report_id' => $this->reportId,
            'widget_type' => 'ReportChart',
            'title' => null,
            'position_x' => ($widgetCount % 3) * 4,
            'position_y' => intdiv($widgetCount, 3) * 2,
            'width' => 4,
            'height' => 2,
            'config' => [],
        ]);

        session()->flash('status', 'Added to dashboard.');
    }

    public function render(ReportService $reportService): View
    {
        $report = Report::query()
            ->select([
                'id',
                'name',
                'description',
                'type',
                'module',
                'filters',
                'group_by',
                'metrics',
                'date_field',
                'date_range',
                'custom_date_from',
                'custom_date_to',
                'owner_id',
            ])
            ->with(['owner:id,full_name'])
            ->findOrFail($this->reportId);

        $chartType = match ($report->type) {
            'Line' => 'line',
            'Pie' => 'pie',
            default => 'bar',
        };

        $chartConfig = [
            'type' => $chartType,
            'data' => $reportService->getData($report),
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ],
        ];

        $tableRows = $report->type === 'Table'
            ? $reportService->getTableData($report)
            : collect();

        return view('reports::livewire.report-view', [
            'chartConfig' => $chartConfig,
            'report' => $report,
            'tableRows' => $tableRows,
        ])->extends('core::layouts.module', ['title' => $report->name]);
    }
}
