<?php

namespace Modules\Invoices\Models;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Database\Factories\InvoiceFactory;
use Modules\Quotes\Models\Quote;

class Invoice extends BaseModel
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'number',
        'quote_id',
        'deal_id',
        'account_id',
        'contact_id',
        'owner_id',
        'status',
        'issue_date',
        'due_date',
        'notes',
        'internal_notes',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_amount',
        'total',
        'amount_paid',
        'currency',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice): void {
            if (blank($invoice->number)) {
                $invoice->number = $invoice->generateNumber();
            }
        });
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
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

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class, 'invoice_id')->orderBy('order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id')->orderByDesc('paid_at');
    }

    public function getBalanceDueAttribute(): float
    {
        return round(max(0, (float) $this->total - (float) $this->amount_paid), 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && ! in_array($this->status, ['Paid', 'Cancelled'], true);
    }

    public function recalculate(): void
    {
        $lineItems = $this->lineItems()
            ->select(['id', 'quantity', 'unit_price', 'discount_percent', 'tax_rate'])
            ->get();

        $subtotal = $lineItems->sum(fn (InvoiceLineItem $item): float => (float) $item->line_total);
        $discountValue = (float) $this->discount_value;
        $discountAmount = $this->discount_type === 'Fixed'
            ? min($discountValue, $subtotal)
            : round($subtotal * max(0, min(100, $discountValue)) / 100, 2);

        $taxAmount = $lineItems->sum(function (InvoiceLineItem $item): float {
            return (float) $item->line_total * ((float) $item->tax_rate / 100);
        });

        $total = max(0, ($subtotal - $discountAmount) + $taxAmount);
        $amountPaid = round((float) $this->payments()->sum('amount'), 2);

        $status = $this->status;

        if ($status !== 'Cancelled') {
            if ($total > 0 && $amountPaid >= $total) {
                $status = 'Paid';
            } elseif ($amountPaid > 0) {
                $status = 'Partially Paid';
            } elseif ($this->status !== 'Draft') {
                $status = $this->due_date !== null && $this->due_date->isPast() ? 'Overdue' : 'Issued';
            }
        }

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
            'amount_paid' => $amountPaid,
            'status' => $status,
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

        return sprintf('INV-%s-%04d', $year, $sequence);
    }

    public function generatePdf(): string
    {
        $this->loadMissing([
            'account:id,name,email,phone,billing_address',
            'contact:id,first_name,last_name,email',
            'owner:id,full_name,email',
            'lineItems:id,invoice_id,name,description,quantity,unit_price,discount_percent,tax_rate,total,order',
            'payments:id,invoice_id,amount,paid_at,method,reference',
        ]);

        $pdf = Pdf::loadView('invoices::pdf.invoice', [
            'invoice' => $this,
            'company' => config('crm.company', []),
            'bankDetails' => data_get(config('crm.company', []), 'bank_details', []),
        ]);

        $path = 'invoices/'.str((string) $this->number)->slug('-').'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        $this->forceFill([
            'pdf_path' => $path,
        ])->saveQuietly();

        return $path;
    }

    /**
     * @param  array{
     *     amount: float|int|string,
     *     paid_at: string,
     *     method: string,
     *     reference?: string|null,
     *     notes?: string|null,
     *     recorded_by: string
     * }  $data
     */
    public function recordPayment(array $data): Payment
    {
        /** @var Payment $payment */
        $payment = $this->payments()->create([
            'amount' => (float) $data['amount'],
            'paid_at' => $data['paid_at'],
            'method' => $data['method'],
            'reference' => filled($data['reference'] ?? null) ? (string) $data['reference'] : null,
            'notes' => filled($data['notes'] ?? null) ? (string) $data['notes'] : null,
            'recorded_by' => (string) $data['recorded_by'],
        ]);

        return $payment;
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNotIn('status', ['Paid', 'Cancelled']);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query
            ->whereColumn('amount_paid', '<', 'total')
            ->whereNotIn('status', ['Paid', 'Cancelled']);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByAccount(Builder $query, string $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    protected static function newFactory(): InvoiceFactory
    {
        return InvoiceFactory::new();
    }
}
