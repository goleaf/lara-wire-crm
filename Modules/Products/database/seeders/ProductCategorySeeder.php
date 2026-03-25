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
        $roots = collect(['Software', 'Services', 'Hardware'])
            ->mapWithKeys(function (string $name): array {
                $category = ProductCategory::query()->updateOrCreate(
                    ['name' => $name],
                    [
                        'description' => $name.' offerings',
                        'parent_id' => null,
                    ]
                );

                return [$name => $category];
            });

        ProductCategory::query()->updateOrCreate(
            ['name' => 'CRM Add-ons'],
            [
                'description' => 'Add-on extensions for CRM platform',
                'parent_id' => (string) $roots['Software']->id,
            ]
        );

        ProductCategory::query()->updateOrCreate(
            ['name' => 'Managed Consulting'],
            [
                'description' => 'Consulting and rollout services',
                'parent_id' => (string) $roots['Services']->id,
            ]
        );
    }
}
