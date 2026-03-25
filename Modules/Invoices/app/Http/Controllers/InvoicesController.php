<?php

namespace Modules\Invoices\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Invoices\Models\Invoice;

class InvoicesController extends Controller
{
    public function downloadPdf(string $id)
    {
        abort_unless(auth()->user()?->can('invoices.view'), 403);

        $invoice = Invoice::query()
            ->select(['id', 'number', 'pdf_path'])
            ->findOrFail($id);

        $path = $invoice->pdf_path ?: $invoice->generatePdf();

        if (! Storage::disk('local')->exists($path)) {
            $path = $invoice->generatePdf();
        }

        return Storage::disk('local')->download($path, $invoice->number.'.pdf');
    }

    public function recordPayment(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        $invoice = Invoice::query()
            ->select(['id', 'amount_paid', 'total', 'status'])
            ->findOrFail($id);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:'.$invoice->balance_due],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:Bank Transfer,Cash,Cheque,Internal Credit'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice->recordPayment($validated + [
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('status', 'Payment recorded.');
    }

    public function cancel(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        Invoice::query()->whereKey($id)->update([
            'status' => 'Cancelled',
        ]);

        return back()->with('status', 'Invoice cancelled.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('invoices.delete'), 403);

        Invoice::query()->whereKey($id)->delete();

        return redirect()
            ->route('invoices.index')
            ->with('status', 'Invoice deleted.');
    }
}
