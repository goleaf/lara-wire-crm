<?php

namespace Modules\Quotes\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuoteService;

class QuotePdfPreview extends Component
{
    public string $quoteId;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('quotes.view'), 403);
        $this->quoteId = $id;
    }

    public function download(QuoteService $quoteService)
    {
        $quote = Quote::query()
            ->select(['id', 'number', 'pdf_path'])
            ->findOrFail($this->quoteId);

        $path = $quote->pdf_path ?: $quoteService->generatePdf($quote);

        if (! Storage::disk('local')->exists($path)) {
            $path = $quoteService->generatePdf($quote);
        }

        return Storage::disk('local')->download($path, $quote->number.'.pdf');
    }

    public function render(): View
    {
        $quote = Quote::query()
            ->select([
                'id',
                'number',
                'name',
                'account_id',
                'contact_id',
                'owner_id',
                'status',
                'valid_until',
                'notes',
                'subtotal',
                'discount_amount',
                'tax_amount',
                'total',
                'currency',
                'created_at',
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name,email',
                'owner:id,full_name,email',
                'lineItems:id,quote_id,name,description,quantity,unit_price,discount_percent,tax_rate,total,order',
            ])
            ->findOrFail($this->quoteId);

        return view('quotes::livewire.quote-pdf-preview', [
            'quote' => $quote,
            'company' => config('crm.company', []),
        ])->extends('core::layouts.module', ['title' => 'Quote Preview']);
    }
}
