<?php

namespace Modules\Quotes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Models\Deal;
use Modules\Quotes\Database\Factories\QuoteFactory;
use Modules\Quotes\Services\QuoteService;

class Quote extends BaseModel
{
    use HasFactory;

    protected $table = 'quotes';

    protected $fillable = [
        'number',
        'name',
        'deal_id',
        'contact_id',
        'account_id',
        'owner_id',
        'status',
        'valid_until',
        'notes',
        'internal_notes',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_amount',
        'total',
        'currency',
        'signed_at',
        'sent_at',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'signed_at' => 'datetime',
            'sent_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(QuoteLineItem::class, 'quote_id')->orderBy('order');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until !== null
            && $this->valid_until->isPast()
            && $this->status !== 'Accepted';
    }

    public function recalculate(): void
    {
        $lineItems = $this->lineItems()
            ->select(['id', 'quantity', 'unit_price', 'discount_percent', 'tax_rate'])
            ->get();

        $subtotal = $lineItems->sum(fn (QuoteLineItem $item): float => (float) $item->line_total);
        $discountValue = (float) $this->discount_value;
        $discountAmount = $this->discount_type === 'Fixed'
            ? min($discountValue, $subtotal)
            : round($subtotal * max(0, min(100, $discountValue)) / 100, 2);

        $taxAmount = $lineItems->sum(function (QuoteLineItem $item): float {
            return (float) $item->line_total * ((float) $item->tax_rate / 100);
        });

        $total = max(0, ($subtotal - $discountAmount) + $taxAmount);

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ])->saveQuietly();
    }

    public function generateNumber(): string
    {
        $year = now()->format('Y');
        $start = now()->copy()->startOfYear();
        $end = now()->copy()->endOfYear();

        $sequence = self::query()
            ->whereBetween('created_at', [$start, $end])
            ->count() + 1;

        return sprintf('QUO-%s-%04d', $year, $sequence);
    }

    public function generatePdf(): string
    {
        return app(QuoteService::class)->generatePdf($this);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByOwner(Builder $query, string $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->whereDate('valid_until', '<', now()->toDateString())
            ->where('status', '!=', 'Accepted');
    }

    protected static function newFactory(): QuoteFactory
    {
        return QuoteFactory::new();
    }
}
