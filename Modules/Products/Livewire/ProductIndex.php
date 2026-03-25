<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;

class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $activeFilter = '';

    public string $recurringFilter = '';

    public string $billingFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('products.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRecurringFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBillingFilter(): void
    {
        $this->resetPage();
    }

    public function toggleActive(string $id): void
    {
        abort_unless(auth()->user()?->can('products.edit'), 403);

        $product = Product::query()->findOrFail($id);
        $product->active = ! $product->active;
        $product->save();

        session()->flash('status', 'Product status updated.');
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->user()?->can('products.delete'), 403);

        Product::query()->whereKey($id)->delete();
        session()->flash('status', 'Product deleted.');

        $this->resetPage();
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
                'cost_price',
                'tax_rate',
                'billing_frequency',
                'active',
                'recurring',
            ])
            ->with('category:id,name,parent_id')
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->categoryFilter !== '', fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->activeFilter !== '', fn ($query) => $query->where('active', $this->activeFilter === '1'))
            ->when($this->recurringFilter !== '', fn ($query) => $query->where('recurring', $this->recurringFilter === '1'))
            ->when($this->billingFilter !== '', fn ($query) => $query->where('billing_frequency', $this->billingFilter))
            ->orderBy('name')
            ->paginate(20);

        $categories = ProductCategory::query()
            ->select(['id', 'name', 'parent_id'])
            ->with('parent:id,name,parent_id')
            ->orderBy('name')
            ->get();

        return view('products::livewire.product-index', [
            'categories' => $categories,
            'products' => $products,
        ])->extends('core::layouts.module', ['title' => 'Products']);
    }
}
