<?php

namespace Modules\Deals\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;

class PipelineStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = PipelineStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'pipeline_id' => Pipeline::factory(),
            'name' => fake()->randomElement(['Prospecting', 'Qualification', 'Proposal', 'Negotiation']),
            'order' => fake()->numberBetween(1, 6),
            'probability' => fake()->numberBetween(0, 100),
            'color' => fake()->hexColor(),
        ];
    }
}
