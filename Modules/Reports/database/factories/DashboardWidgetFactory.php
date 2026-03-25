<?php

namespace Modules\Reports\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Reports\Models\Dashboard;
use Modules\Reports\Models\DashboardWidget;
use Modules\Reports\Models\Report;

class DashboardWidgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = DashboardWidget::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $dashboardId = Dashboard::query()->value('id') ?? Dashboard::factory()->create()->getKey();
        $reportId = Report::query()->value('id') ?? Report::factory()->create()->getKey();

        return [
            'dashboard_id' => (string) $dashboardId,
            'report_id' => (string) $reportId,
            'widget_type' => fake()->randomElement(['ReportChart', 'KPICard', 'ActivityFeed', 'PipelineFunnel', 'QuickStats', 'RecentDeals', 'OpenCases']),
            'title' => fake()->sentence(2),
            'position_x' => 0,
            'position_y' => 0,
            'width' => 4,
            'height' => 2,
            'config' => ['refresh' => '30s'],
        ];
    }
}
