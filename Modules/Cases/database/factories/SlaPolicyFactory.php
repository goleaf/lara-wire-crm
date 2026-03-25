<?php

namespace Modules\Cases\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cases\Models\SlaPolicy;

class SlaPolicyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = SlaPolicy::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => 'SLA '.fake()->word(),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'first_response_hours' => fake()->numberBetween(1, 8),
            'resolution_hours' => fake()->numberBetween(8, 96),
            'is_active' => true,
        ];
    }
}
