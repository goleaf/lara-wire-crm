<?php

namespace Modules\Deals\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;

class PipelineStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pipeline = Pipeline::query()->where('is_default', true)->first();

        if (! $pipeline) {
            return;
        }

        $stages = [
            ['name' => 'Prospecting', 'probability' => 10, 'color' => '#64748b'],
            ['name' => 'Qualification', 'probability' => 25, 'color' => '#3b82f6'],
            ['name' => 'Proposal', 'probability' => 50, 'color' => '#8b5cf6'],
            ['name' => 'Negotiation', 'probability' => 75, 'color' => '#f59e0b'],
            ['name' => 'Closed Won', 'probability' => 100, 'color' => '#10b981'],
            ['name' => 'Closed Lost', 'probability' => 0, 'color' => '#ef4444'],
        ];

        foreach ($stages as $index => $stage) {
            PipelineStage::query()->updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                [
                    'order' => $index + 1,
                    'probability' => $stage['probability'],
                    'color' => $stage['color'],
                ],
            );
        }
    }
}
