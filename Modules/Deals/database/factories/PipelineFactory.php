<?php

namespace Modules\Deals\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Deals\Models\Pipeline;

class PipelineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Pipeline::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'is_default' => false,
            'owner_id' => User::factory(),
        ];
    }
}
