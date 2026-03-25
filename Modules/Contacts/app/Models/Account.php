<?php

namespace Modules\Contacts\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Contacts\Database\Factories\AccountFactory;
use Modules\Core\Models\BaseModel;

class Account extends BaseModel
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'industry',
        'type',
        'website',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'annual_revenue',
        'employee_count',
        'owner_id',
        'parent_account_id',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'billing_address' => 'array',
            'shipping_address' => 'array',
            'tags' => 'array',
            'annual_revenue' => 'decimal:2',
            'employee_count' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'account_id');
    }

    public function getFullBillingAddressAttribute(): string
    {
        $address = $this->billing_address ?? [];

        if (! is_array($address)) {
            return '';
        }

        return collect([
            $address['street'] ?? null,
            $address['city'] ?? null,
            $address['state'] ?? null,
            $address['zip'] ?? null,
            $address['country'] ?? null,
        ])->filter()->implode(', ');
    }

    public function scopeByOwner(Builder $query, string $userId): Builder
    {
        return $query->where('owner_id', $userId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }
}
