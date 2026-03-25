<?php

namespace Modules\Quotes\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuoteService;

class QuoteDetail extends Component
{
    public string $quoteId;

    public ?string $statusMessage = null;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('quotes.view'), 403);
        $this->quoteId = $id;
    }

    public function markAccepted(QuoteService $quoteService): void
    {
        abort_unless(auth()->user()?->can('quotes.edit'), 403);

        $quote = Quote::query()->findOrFail($this->quoteId);
        $quoteService->markAccepted($quote);

        $this->notifyStatusChange('Quote marked as accepted.');
    }

    public function markRejected(QuoteService $quoteService): void
    {
        abort_unless(auth()->user()?->can('quotes.edit'), 403);

        $quote = Quote::query()->findOrFail($this->quoteId);
        $quoteService->markRejected($quote);

        $this->notifyStatusChange('Quote marked as rejected.');
    }

    public function markSent(QuoteService $quoteService): void
    {
        abort_unless(auth()->user()?->can('quotes.edit'), 403);

        $quote = Quote::query()->findOrFail($this->quoteId);
        $quoteService->markSent($quote);

        $this->notifyStatusChange('Quote marked as sent.');
    }

    private function notifyStatusChange(string $message): void
    {
        $this->statusMessage = $message;

        session()->flash('status', $message);
        $this->dispatch('flash', type: 'success', message: $message);
    }

    public function render(): View
    {
        $quote = Quote::query()
            ->select([
                'id',
                'number',
                'name',
                'deal_id',
                'contact_id',
                'account_id',
                'owner_id',
                'status',
                'valid_until',
                'notes',
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
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name,email',
                'deal:id,name',
                'owner:id,full_name',
                'lineItems:id,quote_id,product_id,name,description,quantity,unit_price,discount_percent,tax_rate,total,order',
            ])
            ->findOrFail($this->quoteId);

        return view('quotes::livewire.quote-detail', [
            'quote' => $quote,
        ])->extends('core::layouts.module', ['title' => 'Quote Details']);
    }
}
