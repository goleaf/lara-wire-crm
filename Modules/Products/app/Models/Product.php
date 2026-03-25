<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;
use Modules\Products\Database\Factories\ProductFactory;

class Product extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
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
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'active' => 'boolean',
            'recurring' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getMarginAttribute(): float
    {
        return (float) $this->unit_price - (float) $this->cost_price;
    }

    public function getMarginPercentAttribute(): float
    {
        if ((float) $this->unit_price <= 0) {
            return 0;
        }

        return ($this->margin / (float) $this->unit_price) * 100;
    }

    public function getPriceWithTaxAttribute(): float
    {
        return (float) $this->unit_price * (1 + ((float) $this->tax_rate / 100));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('recurring', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $inner) use ($term): void {
            $inner
                ->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
