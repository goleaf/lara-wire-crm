<?php

namespace Modules\Cases\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cases\Models\SlaPolicy;

class SlaPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            ['priority' => 'Low', 'first_response_hours' => 8, 'resolution_hours' => 72],
            ['priority' => 'Medium', 'first_response_hours' => 4, 'resolution_hours' => 48],
            ['priority' => 'High', 'first_response_hours' => 2, 'resolution_hours' => 24],
            ['priority' => 'Critical', 'first_response_hours' => 1, 'resolution_hours' => 8],
        ];

        foreach ($policies as $policy) {
            SlaPolicy::query()->updateOrCreate(
                ['priority' => $policy['priority']],
                [
                    'name' => $policy['priority'].' Priority SLA',
                    'first_response_hours' => $policy['first_response_hours'],
                    'resolution_hours' => $policy['resolution_hours'],
                    'is_active' => true,
                ]
            );
        }
    }
}
