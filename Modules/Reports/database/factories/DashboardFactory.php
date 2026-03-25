<?php

namespace Modules\Reports\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Reports\Models\Dashboard;

class DashboardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Dashboard::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $ownerId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'name' => fake()->words(2, true),
            'owner_id' => (string) $ownerId,
            'is_default' => false,
            'is_public' => false,
            'layout' => [],
        ];
    }
}
