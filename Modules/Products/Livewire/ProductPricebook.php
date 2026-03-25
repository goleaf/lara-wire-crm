<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Products\Models\Product;

class ProductPricebook extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('products.view'), 403);
    }

    public function render(): View
    {
        $products = Product::query()
            ->select([
                'id',
                'name',
                'sku',
                'category_id',
                'unit_price',
                'tax_rate',
                'currency',
                'unit',
                'recurring',
                'billing_frequency',
                'active',
            ])
            ->with('category:id,name')
            ->active()
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $groupedProducts = $products->groupBy(fn (Product $product): string => $product->category?->name ?? 'Uncategorized');

        return view('products::livewire.product-pricebook', [
            'groupedProducts' => $groupedProducts,
        ])->extends('core::layouts.module', ['title' => 'Product Pricebook']);
    }
}
