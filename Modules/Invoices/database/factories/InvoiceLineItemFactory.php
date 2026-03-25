<?php

namespace Modules\Invoices\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceLineItem;
use Modules\Products\Models\Product;

class InvoiceLineItemFactory extends Factory
{
    protected $model = InvoiceLineItem::class;

    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 10);
        $unitPrice = fake()->randomFloat(2, 10, 500);
        $discount = fake()->randomFloat(2, 0, 20);
        $total = round($quantity * $unitPrice * (1 - ($discount / 100)), 2);
        $invoiceId = Invoice::query()->value('id') ?? Invoice::factory()->create()->getKey();
        $productId = Product::query()->value('id') ?? Product::factory()->create()->getKey();

        return [
            'invoice_id' => (string) $invoiceId,
            'product_id' => (string) $productId,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discount,
            'tax_rate' => fake()->randomFloat(2, 0, 21),
            'total' => $total,
            'order' => 0,
        ];
    }
}
