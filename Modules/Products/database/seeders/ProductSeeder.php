<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ProductCategory::query()
            ->select(['id', 'name'])
            ->get()
            ->keyBy('name');

        $products = [
            ['name' => 'CRM Starter', 'category' => 'Software', 'price' => 79, 'cost' => 24, 'tax' => 21, 'recurring' => true, 'billing' => 'Monthly', 'unit' => 'licenses'],
            ['name' => 'CRM Pro', 'category' => 'Software', 'price' => 149, 'cost' => 52, 'tax' => 21, 'recurring' => true, 'billing' => 'Monthly', 'unit' => 'licenses'],
            ['name' => 'Annual Support', 'category' => 'Services', 'price' => 1900, 'cost' => 840, 'tax' => 21, 'recurring' => true, 'billing' => 'Annual', 'unit' => 'hours'],
            ['name' => 'Implementation Sprint', 'category' => 'Services', 'price' => 4800, 'cost' => 2200, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'hours'],
            ['name' => 'Sales Training Pack', 'category' => 'Services', 'price' => 1200, 'cost' => 350, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'units'],
            ['name' => 'Barcode Scanner', 'category' => 'Hardware', 'price' => 299, 'cost' => 185, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'units'],
            ['name' => 'Desk Phone', 'category' => 'Hardware', 'price' => 189, 'cost' => 120, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'units'],
            ['name' => 'Field Tablet', 'category' => 'Hardware', 'price' => 649, 'cost' => 480, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'units'],
            ['name' => 'Analytics Add-on', 'category' => 'Software', 'price' => 59, 'cost' => 18, 'tax' => 21, 'recurring' => true, 'billing' => 'Monthly', 'unit' => 'licenses'],
            ['name' => 'Data Import Pack', 'category' => 'Services', 'price' => 900, 'cost' => 250, 'tax' => 21, 'recurring' => false, 'billing' => 'One-time', 'unit' => 'units'],
        ];

        foreach ($products as $item) {
            $skuCore = Str::upper(Str::substr(Str::slug($item['name'], ''), 0, 8));
            Product::query()->updateOrCreate(
                ['sku' => 'SKU-'.$skuCore],
                [
                    'name' => $item['name'],
                    'description' => $item['name'].' package',
                    'unit_price' => $item['price'],
                    'cost_price' => $item['cost'],
                    'currency' => config('crm.default_currency_code', 'USD'),
                    'category_id' => $categories[$item['category']]?->id,
                    'tax_rate' => $item['tax'],
                    'active' => true,
                    'recurring' => $item['recurring'],
                    'billing_frequency' => $item['billing'],
                    'unit' => $item['unit'],
                ]
            );
        }
    }
}
