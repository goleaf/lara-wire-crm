<?php

namespace Modules\Leads\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Campaigns\Models\Campaign;
use Modules\Leads\Models\Lead;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->select(['id'])->first();

        if (! $owner) {
            return;
        }

        $campaign = Campaign::query()->firstOrCreate(
            ['name' => 'Inbound Referral Campaign'],
            [
                'type' => 'Referral Program',
                'status' => 'Active',
                'start_date' => now()->subDays(7)->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'budget' => 5000,
                'actual_cost' => 1850,
                'target_audience' => 'SMB decision makers',
                'expected_leads' => 40,
                'description' => 'Seeded campaign used for lead attribution.',
                'owner_id' => $owner->id,
            ]
        );

        Lead::factory()
            ->count(20)
            ->state(fn (): array => [
                'owner_id' => $owner->id,
                'company' => fake()->company(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'campaign_id' => $campaign->getKey(),
                'description' => fake()->paragraph(),
            ])
            ->create();
    }
}
