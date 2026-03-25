<?php

namespace Modules\Quotes\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;

class QuoteLineItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()
            ->select(['id', 'name', 'description', 'unit_price', 'tax_rate'])
            ->limit(20)
            ->get();

        Quote::query()
            ->select(['id'])
            ->get()
            ->each(function (Quote $quote) use ($products): void {
                $lineCount = random_int(1, 3);

                for ($index = 0; $index < $lineCount; $index++) {
                    $product = $products->isNotEmpty() ? $products->random() : null;
                    $quantity = random_int(1, 4);
                    $unitPrice = $product ? (float) $product->unit_price : random_int(50, 500);
                    $discount = random_int(0, 20);

                    $quote->lineItems()->create([
                        'product_id' => $product?->id,
                        'name' => $product?->name ?? 'Custom Item',
                        'description' => $product?->description ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percent' => $discount,
                        'tax_rate' => $product ? (float) $product->tax_rate : 0,
                        'total' => round($quantity * $unitPrice * (1 - ($discount / 100)), 2),
                        'order' => $index,
                    ]);
                }

                $quote->recalculate();
            });
    }
}
