<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Invoices\Database\Factories\InvoiceLineItemFactory;
use Modules\Products\Models\Product;

class InvoiceLineItem extends BaseModel
{
    use HasFactory;

    protected $table = 'invoice_line_items';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'discount_percent',
        'tax_rate',
        'total',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getLineTotalAttribute(): float
    {
        $quantity = (float) $this->quantity;
        $unitPrice = (float) $this->unit_price;
        $discount = min(100, max(0, (float) $this->discount_percent));

        return round($quantity * $unitPrice * (1 - ($discount / 100)), 2);
    }

    protected static function newFactory(): InvoiceLineItemFactory
    {
        return InvoiceLineItemFactory::new();
    }
}
