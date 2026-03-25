<?php

namespace Modules\Quotes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;
use Modules\Products\Models\Product;
use Modules\Quotes\Database\Factories\QuoteLineItemFactory;

class QuoteLineItem extends BaseModel
{
    use HasFactory;

    protected $table = 'quote_line_items';

    protected $fillable = [
        'quote_id',
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
            'order' => 'integer',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getLineTotalAttribute(): float
    {
        $quantity = (float) $this->quantity;
        $unitPrice = (float) $this->unit_price;
        $discount = max(0, min(100, (float) $this->discount_percent));

        return round($quantity * $unitPrice * (1 - ($discount / 100)), 2);
    }

    protected static function newFactory(): QuoteLineItemFactory
    {
        return QuoteLineItemFactory::new();
    }
}
