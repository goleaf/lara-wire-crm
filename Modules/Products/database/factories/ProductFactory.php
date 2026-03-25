<?php

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 25, 2500);
        $costPrice = fake()->randomFloat(2, 5, $unitPrice - 1);
        $name = fake()->unique()->words(2, true);
        $recurring = fake()->boolean(30);

        return [
            'name' => Str::title($name),
            'sku' => 'SKU-'.Str::upper(Str::substr(Str::slug($name, ''), 0, 8)).fake()->numerify('##'),
            'description' => fake()->optional()->sentence(),
            'unit_price' => $unitPrice,
            'cost_price' => $costPrice,
            'currency' => config('crm.default_currency_code', 'USD'),
            'category_id' => ProductCategory::factory(),
            'tax_rate' => fake()->randomElement([0, 5, 10, 15, 21]),
            'active' => fake()->boolean(90),
            'recurring' => $recurring,
            'billing_frequency' => $recurring ? fake()->randomElement(['Monthly', 'Annual']) : 'One-time',
            'unit' => fake()->randomElement(['licenses', 'hours', 'units']),
        ];
    }
}
