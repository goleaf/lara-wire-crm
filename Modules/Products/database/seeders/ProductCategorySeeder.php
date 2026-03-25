<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Software', 'Services', 'Hardware'] as $name) {
            ProductCategory::query()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $name.' offerings',
                    'parent_id' => null,
                ]
            );
        }
    }
}
