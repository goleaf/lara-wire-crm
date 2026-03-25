<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;

class ProductForm extends Component
{
    public ?string $productId = null;

    public string $name = '';

    public string $sku = '';

    public string $description = '';

    public string $unit_price = '0.00';

    public string $cost_price = '0.00';

    public string $currency = '';

    public string $category_id = '';

    public string $tax_rate = '0.00';

    public bool $active = true;

    public bool $recurring = false;

    public ?string $billing_frequency = 'One-time';

    public string $unit = 'units';

    public function mount(?string $id = null): void
    {
        $this->productId = $id;
        $this->currency = config('crm.default_currency_code', 'USD');

        if ($this->productId) {
            abort_unless(auth()->user()?->can('products.edit'), 403);
            $this->loadProduct($this->productId);
        } else {
            abort_unless(auth()->user()?->can('products.create'), 403);
        }
    }

    public function updatedName(string $value): void
    {
        if ($this->productId || $this->sku !== '') {
            return;
        }

        $token = Str::upper(Str::substr(Str::slug($value, ''), 0, 10));
        $this->sku = $token !== '' ? 'SKU-'.$token : '';
    }

    public function updatedRecurring(bool $value): void
    {
        if (! $value) {
            $this->billing_frequency = 'One-time';
        }

        if ($value && ! in_array($this->billing_frequency, ['Monthly', 'Annual'], true)) {
            $this->billing_frequency = 'Monthly';
        }
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $payload = [
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'] !== '' ? $validated['description'] : null,
            'unit_price' => $validated['unit_price'],
            'cost_price' => $validated['cost_price'],
            'currency' => $validated['currency'],
            'category_id' => $validated['category_id'] !== '' ? $validated['category_id'] : null,
            'tax_rate' => $validated['tax_rate'],
            'active' => $validated['active'],
            'recurring' => $validated['recurring'],
            'billing_frequency' => $validated['recurring']
                ? $validated['billing_frequency']
                : 'One-time',
            'unit' => $validated['unit'] !== '' ? $validated['unit'] : null,
        ];

        if ($this->productId) {
            Product::query()->whereKey($this->productId)->update($payload);
            session()->flash('status', 'Product updated.');
        } else {
            Product::query()->create($payload);
            session()->flash('status', 'Product created.');
        }

        $this->redirectRoute('products.index', navigate: true);
    }

    protected function loadProduct(string $id): void
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
            ])
            ->findOrFail($id);

        $this->name = (string) $product->name;
        $this->sku = (string) $product->sku;
        $this->description = (string) ($product->description ?? '');
        $this->unit_price = number_format((float) $product->unit_price, 2, '.', '');
        $this->cost_price = number_format((float) $product->cost_price, 2, '.', '');
        $this->currency = (string) $product->currency;
        $this->category_id = (string) ($product->category_id ?? '');
        $this->tax_rate = number_format((float) $product->tax_rate, 2, '.', '');
        $this->active = (bool) $product->active;
        $this->recurring = (bool) $product->recurring;
        $this->billing_frequency = $product->billing_frequency ?? 'One-time';
        $this->unit = (string) ($product->unit ?? 'units');
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Product::class, 'sku')->ignore($this->productId),
            ],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'category_id' => ['nullable', Rule::exists('product_categories', 'id')],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'active' => ['required', 'boolean'],
            'recurring' => ['required', 'boolean'],
            'billing_frequency' => ['nullable', Rule::in(['One-time', 'Monthly', 'Annual'])],
            'unit' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function render(): View
    {
        $margin = (float) $this->unit_price - (float) $this->cost_price;
        $marginPercent = (float) $this->unit_price > 0
            ? ($margin / (float) $this->unit_price) * 100
            : 0;

        $categories = ProductCategory::query()
            ->select(['id', 'name', 'parent_id'])
            ->with('parent:id,name,parent_id')
            ->orderBy('name')
            ->get();

        return view('products::livewire.product-form', [
            'categories' => $categories,
            'margin' => $margin,
            'marginPercent' => $marginPercent,
        ])->extends('core::layouts.module', [
            'title' => $this->productId ? 'Edit Product' : 'Create Product',
        ]);
    }
}
