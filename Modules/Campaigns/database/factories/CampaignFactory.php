<?php

namespace Modules\Campaigns\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Campaigns\Models\Campaign;

class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Campaign',
            'type' => fake()->randomElement(['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program', 'Other']),
            'status' => fake()->randomElement(['Planned', 'Active', 'Completed', 'Paused']),
            'start_date' => now()->subDays(fake()->numberBetween(0, 30))->toDateString(),
            'end_date' => now()->addDays(fake()->numberBetween(10, 90))->toDateString(),
            'budget' => fake()->randomFloat(2, 500, 15000),
            'actual_cost' => fake()->randomFloat(2, 100, 12000),
            'target_audience' => fake()->sentence(),
            'expected_leads' => fake()->numberBetween(10, 120),
            'description' => fake()->paragraph(),
            'owner_id' => User::query()->value('id'),
        ];
    }
}
