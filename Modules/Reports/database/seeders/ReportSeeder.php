<?php

namespace Modules\Reports\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Reports\Models\Report;

class ReportSeeder extends Seeder
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

        $reports = [
            ['name' => 'Sales Pipeline by Stage', 'type' => 'Funnel', 'module' => 'Deals', 'group_by' => 'stage_id', 'metrics' => ['dealsByStage']],
            ['name' => 'Revenue by Month', 'type' => 'Line', 'module' => 'Deals', 'group_by' => null, 'metrics' => ['revenueByMonth']],
            ['name' => 'Won vs Lost Deals', 'type' => 'Bar', 'module' => 'Deals', 'group_by' => 'status', 'metrics' => ['winRate']],
            ['name' => 'Lead Conversion Rate', 'type' => 'KPI', 'module' => 'Leads', 'group_by' => null, 'metrics' => ['conversionRate']],
            ['name' => 'Lead Source Distribution', 'type' => 'Pie', 'module' => 'Leads', 'group_by' => 'lead_source', 'metrics' => ['leadsBySource']],
            ['name' => 'Activities by Type This Week', 'type' => 'Bar', 'module' => 'Activities', 'group_by' => 'type', 'metrics' => ['byType']],
            ['name' => 'Case Status Overview', 'type' => 'Pie', 'module' => 'Cases', 'group_by' => 'status', 'metrics' => ['casesByStatus']],
            ['name' => 'Case Resolution Time by Priority', 'type' => 'Bar', 'module' => 'Cases', 'group_by' => 'priority', 'metrics' => ['avgResolutionHours']],
            ['name' => 'Invoice Aging Summary', 'type' => 'Table', 'module' => 'Invoices', 'group_by' => 'status', 'metrics' => ['overdueAmount']],
            ['name' => 'Campaign ROI Comparison', 'type' => 'Bar', 'module' => 'Campaigns', 'group_by' => 'name', 'metrics' => ['roi']],
            ['name' => 'Top Products by Revenue', 'type' => 'Bar', 'module' => 'Products', 'group_by' => 'name', 'metrics' => ['sum:unit_price']],
        ];

        foreach ($reports as $report) {
            Report::query()->updateOrCreate(
                ['name' => $report['name']],
                [
                    'description' => $report['name'].' (seeded report)',
                    'type' => $report['type'],
                    'module' => $report['module'],
                    'filters' => [],
                    'group_by' => $report['group_by'],
                    'metrics' => $report['metrics'],
                    'date_field' => 'created_at',
                    'date_range' => 'This Month',
                    'custom_date_from' => null,
                    'custom_date_to' => null,
                    'is_scheduled' => false,
                    'schedule_frequency' => null,
                    'owner_id' => $ownerId,
                    'is_public' => true,
                ]
            );
        }
    }
}
