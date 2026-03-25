<?php

namespace Modules\Deals\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Database\Factories\DealFactory;
use Modules\Invoices\Models\Invoice;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;

class Deal extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'deals';

    protected $fillable = [
        'name',
        'account_id',
        'contact_id',
        'owner_id',
        'pipeline_id',
        'stage_id',
        'amount',
        'currency',
        'probability',
        'expected_revenue',
        'close_date',
        'deal_type',
        'lost_reason',
        'lost_notes',
        'source',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expected_revenue' => 'decimal:2',
            'probability' => 'integer',
            'close_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'deal_products')
            ->withPivot(['quantity', 'unit_price', 'discount', 'total'])
            ->withTimestamps();
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'deal_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'deal_id');
    }

    public function getExpectedRevenueAttribute(): float
    {
        return round(((float) $this->amount * (int) $this->probability) / 100, 2);
    }

    public function getIsWonAttribute(): bool
    {
        return $this->stage?->name === 'Closed Won';
    }

    public function getIsLostAttribute(): bool
    {
        return $this->stage?->name === 'Closed Lost';
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereHas('stage', fn (Builder $inner) => $inner->whereNotIn('name', ['Closed Won', 'Closed Lost']));
    }

    public function scopeWon(Builder $query): Builder
    {
        return $query->whereHas('stage', fn (Builder $inner) => $inner->where('name', 'Closed Won'));
    }

    public function scopeLost(Builder $query): Builder
    {
        return $query->whereHas('stage', fn (Builder $inner) => $inner->where('name', 'Closed Lost'));
    }

    public function scopeByOwner(Builder $query, string $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPipeline(Builder $query, string $pipelineId): Builder
    {
        return $query->where('pipeline_id', $pipelineId);
    }

    protected static function newFactory(): DealFactory
    {
        return DealFactory::new();
    }
}
