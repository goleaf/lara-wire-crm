<?php

namespace Modules\Quotes\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuoteService;

class QuoteForm extends Component
{
    public ?string $quoteId = null;

    public string $name = '';

    public string $deal_id = '';

    public string $contact_id = '';

    public string $account_id = '';

    public string $owner_id = '';

    public string $status = 'Draft';

    public string $valid_until = '';

    public string $currency = '';

    public string $notes = '';

    public string $internal_notes = '';

    public string $discount_type = 'Percentage';

    public string $discount_value = '0';

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
        $this->quoteId = $id;
        $this->owner_id = (string) auth()->id();
        $this->currency = config('crm.default_currency.code', 'USD');
        $this->valid_until = now()->addDays(15)->toDateString();

        if ($this->quoteId) {
            abort_unless(auth()->user()?->can('quotes.edit'), 403);
            $this->loadQuote();

            return;
        }

        abort_unless(auth()->user()?->can('quotes.create'), 403);
        $this->lineItems = [[
            'product_id' => null,
            'name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'tax_rate' => 0,
            'total' => 0,
        ]];
    }

    #[On('lineItemsUpdated')]
    public function syncLineItems(array $items): void
    {
        $this->lineItems = $items;
        $this->recalculateTotals();
    }

    public function saveDraft(): void
    {
        $quote = $this->persist('Draft');

        session()->flash('status', 'Quote saved.');
        $this->redirectRoute('quotes.show', ['id' => $quote->getKey()], navigate: true);
    }

    public function markSent(QuoteService $quoteService): void
    {
        $quote = $this->persist('Sent');
        $quoteService->markSent($quote);

        session()->flash('status', 'Quote marked as sent.');
        $this->redirectRoute('quotes.show', ['id' => $quote->getKey()], navigate: true);
    }

    public function previewPdf(QuoteService $quoteService): void
    {
        $quote = $this->persist($this->status);
        $quoteService->generatePdf($quote);

        $this->redirectRoute('quotes.preview', ['id' => $quote->getKey()], navigate: true);
    }

    public function render(): View
    {
        $this->recalculateTotals();

        return view('quotes::livewire.quote-form', [
            'accounts' => Account::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'contacts' => Contact::query()->select(['id', 'first_name', 'last_name'])->orderBy('first_name')->limit(100)->get(),
            'deals' => Deal::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
        ])->extends('core::layouts.module', [
            'title' => $this->quoteId ? 'Edit Quote' : 'Create Quote',
        ]);
    }

    protected function loadQuote(): void
    {
        $quote = Quote::query()
            ->select([
                'id',
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
            ])
            ->with('lineItems:id,quote_id,product_id,name,quantity,unit_price,discount_percent,tax_rate,total,order')
            ->findOrFail($this->quoteId);

        $this->name = (string) $quote->name;
        $this->deal_id = (string) ($quote->deal_id ?? '');
        $this->contact_id = (string) ($quote->contact_id ?? '');
        $this->account_id = (string) ($quote->account_id ?? '');
        $this->owner_id = (string) $quote->owner_id;
        $this->status = (string) $quote->status;
        $this->valid_until = $quote->valid_until?->toDateString() ?? '';
        $this->currency = (string) $quote->currency;
        $this->notes = (string) ($quote->notes ?? '');
        $this->internal_notes = (string) ($quote->internal_notes ?? '');
        $this->discount_type = (string) $quote->discount_type;
        $this->discount_value = (string) $quote->discount_value;

        $this->lineItems = $quote->lineItems
            ->map(fn ($lineItem) => [
                'product_id' => $lineItem->product_id,
                'name' => (string) $lineItem->name,
                'quantity' => (float) $lineItem->quantity,
                'unit_price' => (float) $lineItem->unit_price,
                'discount' => (float) $lineItem->discount_percent,
                'tax_rate' => (float) $lineItem->tax_rate,
                'total' => (float) $lineItem->total,
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

    protected function persist(string $targetStatus): Quote
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'deal_id' => ['nullable', Rule::exists('deals', 'id')],
            'contact_id' => ['nullable', Rule::exists('contacts', 'id')],
            'account_id' => ['nullable', Rule::exists('accounts', 'id')],
            'owner_id' => ['required', Rule::exists('users', 'id')],
            'status' => ['required', Rule::in(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])],
            'valid_until' => ['nullable', 'date'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(['Percentage', 'Fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
        ]);

        $quote = Quote::query()->updateOrCreate(
            ['id' => $this->quoteId],
            [
                'name' => $validated['name'],
                'deal_id' => $this->nullableString($validated['deal_id']),
                'contact_id' => $this->nullableString($validated['contact_id']),
                'account_id' => $this->nullableString($validated['account_id']),
                'owner_id' => $validated['owner_id'],
                'status' => $targetStatus,
                'valid_until' => $this->nullableString($validated['valid_until']),
                'currency' => $validated['currency'],
                'notes' => $this->nullableString($validated['notes']),
                'internal_notes' => $this->nullableString($validated['internal_notes']),
                'discount_type' => $validated['discount_type'],
                'discount_value' => (float) $validated['discount_value'],
            ]
        );

        $quote->lineItems()->delete();

        foreach (array_values($this->lineItems) as $index => $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $quantity = max(0, (float) ($item['quantity'] ?? 0));
            $unitPrice = max(0, (float) ($item['unit_price'] ?? 0));
            $discount = max(0, min(100, (float) ($item['discount'] ?? 0)));
            $taxRate = max(0, min(100, (float) ($item['tax_rate'] ?? 0)));

            $quote->lineItems()->create([
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

        $quote->recalculate();

        $this->quoteId = (string) $quote->getKey();

        return $quote->fresh();
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
