<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Products\Database\Factories\ProductCategoryFactory;

class ProductCategory extends BaseModel
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getFullPathAttribute(): string
    {
        $segments = [$this->name];
        $node = $this->parent;
        $depth = 0;

        while ($node && $depth < 10) {
            array_unshift($segments, $node->name);
            $node = $node->parent;
            $depth++;
        }

        return implode(' > ', array_filter($segments));
    }

    protected static function newFactory(): ProductCategoryFactory
    {
        return ProductCategoryFactory::new();
    }
}
