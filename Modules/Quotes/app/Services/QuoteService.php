<?php

namespace Modules\Quotes\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceLineItem;
use Modules\Quotes\Models\Quote;
use RuntimeException;

class QuoteService
{
    public function duplicate(Quote $quote): Quote
    {
        $quote->loadMissing('lineItems');

        $newQuote = Quote::query()->create([
            'number' => (new Quote)->generateNumber(),
            'name' => $quote->name.' (Copy)',
            'deal_id' => $quote->deal_id,
            'contact_id' => $quote->contact_id,
            'account_id' => $quote->account_id,
            'owner_id' => $quote->owner_id,
            'status' => 'Draft',
            'valid_until' => $quote->valid_until,
            'notes' => $quote->notes,
            'internal_notes' => $quote->internal_notes,
            'discount_type' => $quote->discount_type,
            'discount_value' => $quote->discount_value,
            'currency' => $quote->currency,
        ]);

        foreach ($quote->lineItems as $index => $lineItem) {
            $newQuote->lineItems()->create([
                'product_id' => $lineItem->product_id,
                'name' => $lineItem->name,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'discount_percent' => $lineItem->discount_percent,
                'tax_rate' => $lineItem->tax_rate,
                'total' => $lineItem->line_total,
                'order' => $index,
            ]);
        }

        $newQuote->recalculate();

        return $newQuote;
    }

    /**
     * @return mixed
     */
    public function convertToInvoice(Quote $quote)
    {
        if (! class_exists(Invoice::class) || ! class_exists(InvoiceLineItem::class)) {
            throw new RuntimeException('Invoices module is not available yet.');
        }

        $quote->loadMissing('lineItems');

        $invoice = Invoice::query()->create([
            'quote_id' => $quote->id,
            'deal_id' => $quote->deal_id,
            'account_id' => $quote->account_id,
            'contact_id' => $quote->contact_id,
            'owner_id' => $quote->owner_id,
            'status' => 'Issued',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'notes' => $quote->notes,
            'internal_notes' => $quote->internal_notes,
            'discount_type' => $quote->discount_type,
            'discount_value' => $quote->discount_value,
            'currency' => $quote->currency,
        ]);

        foreach ($quote->lineItems as $index => $lineItem) {
            InvoiceLineItem::query()->create([
                'invoice_id' => $invoice->id,
                'product_id' => $lineItem->product_id,
                'name' => $lineItem->name,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'discount_percent' => $lineItem->discount_percent,
                'tax_rate' => $lineItem->tax_rate,
                'total' => $lineItem->line_total,
                'order' => $index,
            ]);
        }

        if (method_exists($invoice, 'recalculate')) {
            $invoice->recalculate();
        }

        return $invoice;
    }

    public function generatePdf(Quote $quote): string
    {
        $quote->loadMissing([
            'account:id,name',
            'contact:id,first_name,last_name,email',
            'owner:id,full_name,email',
            'lineItems:id,quote_id,name,description,quantity,unit_price,discount_percent,tax_rate,total,order',
        ]);

        $pdf = Pdf::loadView('quotes::pdf.quote', [
            'quote' => $quote,
            'company' => config('crm.company', []),
        ]);

        $path = 'quotes/'.str((string) $quote->number)->slug('-').'.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        $quote->forceFill([
            'pdf_path' => $path,
        ])->saveQuietly();

        return $path;
    }

    public function markSent(Quote $quote): void
    {
        $quote->forceFill([
            'status' => 'Sent',
            'sent_at' => now(),
        ])->save();
    }

    public function markAccepted(Quote $quote): void
    {
        $quote->forceFill([
            'status' => 'Accepted',
            'signed_at' => now(),
        ])->save();
    }

    public function markRejected(Quote $quote): void
    {
        $quote->forceFill([
            'status' => 'Rejected',
        ])->save();
    }
}
