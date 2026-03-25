<?php

namespace Modules\Products\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Products\Models\Product;

#[Defer]
class ProductLineItem extends Component
{
    /**
     * @var array<int, array{
     *     product_id: string|null,
     *     name: string,
     *     quantity: float|int|string,
     *     unit_price: float|int|string,
     *     discount: float|int|string,
     *     tax_rate: float|int|string,
     *     total: float|int|string
     * }>
     */
    public array $lineItems = [];

    public bool $editable = true;

    public function mount(array $lineItems = [], bool $editable = true): void
    {
        $this->editable = $editable;
        $this->lineItems = $lineItems;

        if ($this->lineItems === [] && $this->editable) {
            $this->addRow();
        }

        $this->recalculate();
    }

    public function addRow(): void
    {
        if (! $this->editable) {
            return;
        }

        $this->lineItems[] = [
            'product_id' => null,
            'name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'tax_rate' => 0,
            'total' => 0,
        ];

        $this->recalculate();
    }

    public function removeRow(int $index): void
    {
        if (! $this->editable) {
            return;
        }

        if (! array_key_exists($index, $this->lineItems)) {
            return;
        }

        unset($this->lineItems[$index]);
        $this->lineItems = array_values($this->lineItems);

        $this->recalculate();
    }

    public function updatedLineItems(): void
    {
        $this->recalculate();
    }

    public function useProduct(int $index, string $productId): void
    {
        if (! array_key_exists($index, $this->lineItems)) {
            return;
        }

        $product = Product::query()
            ->select(['id', 'name', 'unit_price', 'tax_rate'])
            ->findOrFail($productId);

        $this->lineItems[$index]['product_id'] = $product->id;
        $this->lineItems[$index]['name'] = $product->name;
        $this->lineItems[$index]['unit_price'] = (float) $product->unit_price;
        $this->lineItems[$index]['tax_rate'] = (float) $product->tax_rate;

        $this->recalculate();
    }

    protected function recalculate(): void
    {
        foreach ($this->lineItems as $index => $item) {
            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = min(100, max(0, (float) ($item['discount'] ?? 0)));

            $lineTotal = $quantity * $unitPrice * (1 - ($discount / 100));
            $this->lineItems[$index]['total'] = round($lineTotal, 2);
        }

        $this->dispatch('lineItemsUpdated', items: $this->lineItems);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->lineItems)->sum(fn (array $item): float => (float) ($item['total'] ?? 0));
    }

    public function getDiscountTotalProperty(): float
    {
        return collect($this->lineItems)->sum(function (array $item): float {
            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = min(100, max(0, (float) ($item['discount'] ?? 0)));

            return ($quantity * $unitPrice) * ($discount / 100);
        });
    }

    public function getTaxTotalProperty(): float
    {
        return collect($this->lineItems)->sum(function (array $item): float {
            $lineTotal = (float) ($item['total'] ?? 0);
            $taxRate = max(0, (float) ($item['tax_rate'] ?? 0));

            return $lineTotal * ($taxRate / 100);
        });
    }

    public function getGrandTotalProperty(): float
    {
        return $this->subtotal + $this->taxTotal;
    }

    public function render(): View
    {
        $products = Product::query()
            ->select(['id', 'name', 'sku', 'unit_price', 'tax_rate'])
            ->active()
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('products::livewire.product-line-item', [
            'products' => $products,
        ]);
    }
}
