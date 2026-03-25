<?php

namespace Modules\Quotes\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuoteService;

class QuotesController extends Controller
{
    public function downloadPdf(string $id, QuoteService $quoteService)
    {
        abort_unless(auth()->user()?->can('quotes.view'), 403);

        $quote = Quote::query()
            ->select(['id', 'number', 'pdf_path'])
            ->findOrFail($id);

        $path = $quote->pdf_path ?: $quoteService->generatePdf($quote);

        if (! Storage::disk('local')->exists($path)) {
            $path = $quoteService->generatePdf($quote);
        }

        return Storage::disk('local')->download($path, $quote->number.'.pdf');
    }

    public function duplicate(string $id, QuoteService $quoteService): RedirectResponse
    {
        abort_unless(auth()->user()?->can('quotes.create'), 403);

        $quote = Quote::query()
            ->select(['id', 'name', 'deal_id', 'contact_id', 'account_id', 'owner_id', 'status', 'valid_until', 'notes', 'internal_notes', 'discount_type', 'discount_value', 'currency'])
            ->with('lineItems:id,quote_id,product_id,name,description,quantity,unit_price,discount_percent,tax_rate,total')
            ->findOrFail($id);

        $duplicate = $quoteService->duplicate($quote);

        return redirect()
            ->route('quotes.edit', ['id' => $duplicate->getKey()])
            ->with('status', 'Quote duplicated.');
    }

    public function convertToInvoice(string $id, QuoteService $quoteService): RedirectResponse
    {
        abort_unless(auth()->user()?->can('quotes.create'), 403);

        $quote = Quote::query()
            ->select(['id', 'deal_id', 'contact_id', 'account_id', 'owner_id', 'notes', 'internal_notes', 'discount_type', 'discount_value', 'currency'])
            ->with('lineItems:id,quote_id,product_id,name,description,quantity,unit_price,discount_percent,tax_rate,total')
            ->findOrFail($id);

        try {
            $invoice = $quoteService->convertToInvoice($quote);
        } catch (\RuntimeException $exception) {
            return back()->with('status', $exception->getMessage());
        }

        if (Route::has('invoices.show')) {
            return redirect()
                ->route('invoices.show', ['id' => $invoice->getKey()])
                ->with('status', 'Quote converted to invoice.');
        }

        return back()->with('status', 'Quote converted to invoice.');
    }

    public function updateStatus(string $id, Request $request, QuoteService $quoteService): RedirectResponse
    {
        abort_unless(auth()->user()?->can('quotes.edit'), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:Draft,Sent,Accepted,Rejected,Expired'],
        ]);

        $quote = Quote::query()->findOrFail($id);

        match ($validated['status']) {
            'Sent' => $quoteService->markSent($quote),
            'Accepted' => $quoteService->markAccepted($quote),
            'Rejected' => $quoteService->markRejected($quote),
            default => $quote->update(['status' => $validated['status']]),
        };

        return back()->with('status', 'Quote status updated.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('quotes.delete'), 403);

        Quote::query()->whereKey($id)->delete();

        return redirect()
            ->route('quotes.index')
            ->with('status', 'Quote deleted.');
    }
}
