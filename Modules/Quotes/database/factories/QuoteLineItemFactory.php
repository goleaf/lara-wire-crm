<?php

namespace Modules\Quotes\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Models\QuoteLineItem;

class QuoteLineItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = QuoteLineItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 5);
        $unitPrice = $this->faker->randomFloat(2, 20, 400);
        $discount = $this->faker->randomFloat(2, 0, 25);

        return [
            'quote_id' => Quote::query()->value('id') ?? Str::uuid()->toString(),
            'product_id' => Product::query()->value('id'),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discount,
            'tax_rate' => $this->faker->randomElement([0, 5, 10, 21]),
            'total' => round($quantity * $unitPrice * (1 - ($discount / 100)), 2),
            'order' => 0,
        ];
    }
}
