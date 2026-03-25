<?php

namespace Modules\Reports\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Reports\Models\Dashboard;
use Modules\Reports\Models\DashboardWidget;
use Modules\Reports\Models\Report;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::query()->value('id');

        if ($ownerId === null) {
            return;
        }

        $dashboard = Dashboard::query()->updateOrCreate(
            ['owner_id' => $ownerId, 'is_default' => true],
            [
                'name' => 'Default Dashboard',
                'is_public' => true,
                'layout' => [],
            ]
        );

        $widgetTemplates = [
            ['widget_type' => 'KPICard', 'title' => 'Pipeline KPI', 'position_x' => 0, 'position_y' => 0, 'width' => 2, 'height' => 1],
            ['widget_type' => 'PipelineFunnel', 'title' => 'Pipeline Funnel', 'position_x' => 2, 'position_y' => 0, 'width' => 4, 'height' => 2],
            ['widget_type' => 'ActivityFeed', 'title' => 'Recent Activities', 'position_x' => 6, 'position_y' => 0, 'width' => 3, 'height' => 2],
            ['widget_type' => 'RecentDeals', 'title' => 'Recent Deals', 'position_x' => 9, 'position_y' => 0, 'width' => 3, 'height' => 2],
            ['widget_type' => 'OpenCases', 'title' => 'Open Cases', 'position_x' => 0, 'position_y' => 2, 'width' => 4, 'height' => 2],
            ['widget_type' => 'ReportChart', 'title' => 'Revenue by Month', 'position_x' => 4, 'position_y' => 2, 'width' => 8, 'height' => 3],
        ];

        $reportForChart = Report::query()
            ->select(['id', 'name'])
            ->where('name', 'Revenue by Month')
            ->first();

        foreach ($widgetTemplates as $index => $widgetTemplate) {
            DashboardWidget::query()->updateOrCreate(
                [
                    'dashboard_id' => $dashboard->id,
                    'position_x' => $widgetTemplate['position_x'],
                    'position_y' => $widgetTemplate['position_y'],
                ],
                [
                    'report_id' => $widgetTemplate['widget_type'] === 'ReportChart' ? $reportForChart?->id : null,
                    'widget_type' => $widgetTemplate['widget_type'],
                    'title' => $widgetTemplate['title'],
                    'width' => $widgetTemplate['width'],
                    'height' => $widgetTemplate['height'],
                    'config' => ['seed_order' => $index],
                ]
            );
        }
    }
}
