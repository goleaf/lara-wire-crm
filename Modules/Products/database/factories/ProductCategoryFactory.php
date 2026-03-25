<?php

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'parent_id' => null,
        ];
    }
}
