<?php

namespace Modules\Invoices\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Quotes\Models\Quote;

class InvoiceForm extends Component
{
    public ?string $invoiceId = null;

    public string $quote_id = '';

    public string $deal_id = '';

    public string $account_id = '';

    public string $contact_id = '';

    public string $owner_id = '';

    public string $status = 'Draft';

    public string $issue_date = '';

    public string $due_date = '';

    public string $notes = '';

    public string $internal_notes = '';

    public string $discount_type = 'Percentage';

    public string $discount_value = '0';

    public string $currency = '';

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

    public float $subtotal = 0;

    public float $discount_amount = 0;

    public float $tax_amount = 0;

    public float $grand_total = 0;

    public function mount(?string $id = null): void
    {
        $this->invoiceId = $id;
        $this->owner_id = (string) auth()->id();
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(30)->toDateString();
        $this->currency = config('crm.default_currency.code', 'USD');

        if ($this->invoiceId !== null) {
            abort_unless(auth()->user()?->can('invoices.edit'), 403);
            $this->loadInvoice();

            return;
        }

        abort_unless(auth()->user()?->can('invoices.create'), 403);

        $this->lineItems = [[
            'product_id' => null,
            'name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'tax_rate' => 0,
            'total' => 0,
        ]];

        $quoteId = (string) request()->query('quote', '');

        if ($quoteId !== '' && class_exists(Quote::class)) {
            $this->hydrateFromQuote($quoteId);
        }
    }

    #[On('lineItemsUpdated')]
    public function syncLineItems(array $items): void
    {
        $this->lineItems = $items;
        $this->recalculateTotals();
    }

    public function setDueInDays(int $days): void
    {
        $this->due_date = now()->addDays($days)->toDateString();
    }

    public function saveDraft(): void
    {
        $invoice = $this->persist('Draft');

        session()->flash('status', 'Invoice draft saved.');
        $this->redirectRoute('invoices.show', ['id' => $invoice->getKey()], navigate: true);
    }

    public function saveIssued(): void
    {
        $invoice = $this->persist('Issued');

        session()->flash('status', 'Invoice issued.');
        $this->redirectRoute('invoices.show', ['id' => $invoice->getKey()], navigate: true);
    }

    public function render(): View
    {
        $this->recalculateTotals();

        return view('invoices::livewire.invoice-form', [
            'accounts' => Account::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'contacts' => Contact::query()->select(['id', 'first_name', 'last_name'])->orderBy('first_name')->limit(100)->get(),
            'deals' => Deal::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
        ])->extends('core::layouts.module', [
            'title' => $this->invoiceId ? 'Edit Invoice' : 'Create Invoice',
        ]);
    }

    protected function loadInvoice(): void
    {
        $invoice = Invoice::query()
            ->select([
                'id',
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
                'discount_type',
                'discount_value',
                'currency',
            ])
            ->with('lineItems:id,invoice_id,product_id,name,quantity,unit_price,discount_percent,tax_rate,total,order')
            ->findOrFail($this->invoiceId);

        $this->quote_id = (string) ($invoice->quote_id ?? '');
        $this->deal_id = (string) ($invoice->deal_id ?? '');
        $this->account_id = (string) $invoice->account_id;
        $this->contact_id = (string) ($invoice->contact_id ?? '');
        $this->owner_id = (string) $invoice->owner_id;
        $this->status = (string) $invoice->status;
        $this->issue_date = $invoice->issue_date?->toDateString() ?? now()->toDateString();
        $this->due_date = $invoice->due_date?->toDateString() ?? now()->addDays(30)->toDateString();
        $this->notes = (string) ($invoice->notes ?? '');
        $this->internal_notes = (string) ($invoice->internal_notes ?? '');
        $this->discount_type = (string) $invoice->discount_type;
        $this->discount_value = (string) $invoice->discount_value;
        $this->currency = (string) $invoice->currency;

        $this->lineItems = $invoice->lineItems
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => (string) $item->name,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount' => (float) $item->discount_percent,
                'tax_rate' => (float) $item->tax_rate,
                'total' => (float) $item->total,
            ])
            ->values()
            ->all();

        if ($this->lineItems === []) {
            $this->lineItems[] = [
                'product_id' => null,
                'name' => '',
                'quantity' => 1,
                'unit_price' => 0,
                'discount' => 0,
                'tax_rate' => 0,
                'total' => 0,
            ];
        }
    }

    protected function hydrateFromQuote(string $quoteId): void
    {
        /** @var Quote|null $quote */
        $quote = Quote::query()
            ->select([
                'id',
                'deal_id',
                'contact_id',
                'account_id',
                'owner_id',
                'notes',
                'internal_notes',
                'discount_type',
                'discount_value',
                'currency',
            ])
            ->with('lineItems:id,quote_id,product_id,name,quantity,unit_price,discount_percent,tax_rate,total,order')
            ->find($quoteId);

        if (! $quote) {
            return;
        }

        $this->quote_id = (string) $quote->id;
        $this->deal_id = (string) ($quote->deal_id ?? '');
        $this->contact_id = (string) ($quote->contact_id ?? '');
        $this->account_id = (string) ($quote->account_id ?? '');
        $this->owner_id = (string) $quote->owner_id;
        $this->notes = (string) ($quote->notes ?? '');
        $this->internal_notes = (string) ($quote->internal_notes ?? '');
        $this->discount_type = (string) $quote->discount_type;
        $this->discount_value = (string) $quote->discount_value;
        $this->currency = (string) $quote->currency;
        $this->status = 'Issued';

        $this->lineItems = $quote->lineItems
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => (string) $item->name,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount' => (float) $item->discount_percent,
                'tax_rate' => (float) $item->tax_rate,
                'total' => (float) $item->total,
            ])
            ->values()
            ->all();
    }

    protected function persist(string $targetStatus): Invoice
    {
        $validated = $this->validate([
            'quote_id' => ['nullable', Rule::exists('quotes', 'id')],
            'deal_id' => ['nullable', Rule::exists('deals', 'id')],
            'account_id' => ['required', Rule::exists('accounts', 'id')],
            'contact_id' => ['nullable', Rule::exists('contacts', 'id')],
            'owner_id' => ['required', Rule::exists('users', 'id')],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(['Percentage', 'Fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        $invoice = Invoice::query()->updateOrCreate(
            ['id' => $this->invoiceId],
            [
                'quote_id' => $this->nullableString($validated['quote_id']),
                'deal_id' => $this->nullableString($validated['deal_id']),
                'account_id' => $validated['account_id'],
                'contact_id' => $this->nullableString($validated['contact_id']),
                'owner_id' => $validated['owner_id'],
                'status' => $targetStatus,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'notes' => $this->nullableString($validated['notes']),
                'internal_notes' => $this->nullableString($validated['internal_notes']),
                'discount_type' => $validated['discount_type'],
                'discount_value' => (float) $validated['discount_value'],
                'currency' => $validated['currency'],
            ]
        );

        $invoice->lineItems()->delete();

        foreach (array_values($this->lineItems) as $index => $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = max(0, min(100, (float) ($item['discount'] ?? 0)));
            $taxRate = max(0, min(100, (float) ($item['tax_rate'] ?? 0)));

            $invoice->lineItems()->create([
                'product_id' => $this->nullableString($item['product_id'] ?? null),
                'name' => $name,
                'description' => null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percent' => $discount,
                'tax_rate' => $taxRate,
                'total' => round($quantity * $unitPrice * (1 - ($discount / 100)), 2),
                'order' => $index,
            ]);
        }

        $invoice->recalculate();
        $this->invoiceId = (string) $invoice->getKey();

        return $invoice->fresh();
    }

    protected function recalculateTotals(): void
    {
        $subtotal = collect($this->lineItems)->sum(function (array $item): float {
            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = max(0, min(100, (float) ($item['discount'] ?? 0)));

            return $quantity * $unitPrice * (1 - ($discount / 100));
        });

        $taxAmount = collect($this->lineItems)->sum(function (array $item): float {
            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = max(0, min(100, (float) ($item['discount'] ?? 0)));
            $taxRate = max(0, min(100, (float) ($item['tax_rate'] ?? 0)));
            $lineTotal = $quantity * $unitPrice * (1 - ($discount / 100));

            return $lineTotal * ($taxRate / 100);
        });

        $discountValue = max(0, (float) $this->discount_value);
        $discountAmount = $this->discount_type === 'Fixed'
            ? min($discountValue, $subtotal)
            : $subtotal * min(100, $discountValue) / 100;

        $this->subtotal = round($subtotal, 2);
        $this->tax_amount = round($taxAmount, 2);
        $this->discount_amount = round($discountAmount, 2);
        $this->grand_total = round(max(0, ($subtotal - $discountAmount) + $taxAmount), 2);
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
