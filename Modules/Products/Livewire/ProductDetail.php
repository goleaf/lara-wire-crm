<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Products\Models\Product;

class ProductDetail extends Component
{
    public string $productId;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('products.view'), 403);
        $this->productId = $id;
    }

    public function render(): View
    {
        $product = Product::query()
            ->select([
                'id',
                'name',
                'sku',
                'description',
                'unit_price',
                'cost_price',
                'currency',
                'category_id',
                'tax_rate',
                'active',
                'recurring',
                'billing_frequency',
                'unit',
                'created_at',
                'updated_at',
            ])
            ->with('category:id,name,parent_id')
            ->findOrFail($this->productId);

        return view('products::livewire.product-detail', [
            'product' => $product,
        ])->extends('core::layouts.module', ['title' => 'Product Detail']);
    }
}
