<?php

namespace Modules\Invoices\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Invoices\Models\Invoice;

class InvoiceDetail extends Component
{
    public string $invoiceId = '';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('invoices.view'), 403);
        $this->invoiceId = $id;
    }

    #[On('payment-recorded')]
    public function refreshInvoice(): void
    {
        // Intentional no-op to trigger a component re-render.
    }

    public function cancelInvoice(): void
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        Invoice::query()->whereKey($this->invoiceId)->update(['status' => 'Cancelled']);

        session()->flash('status', 'Invoice cancelled.');
    }

    public function render(): View
    {
        $invoice = Invoice::query()
            ->select([
                'id',
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
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name,email',
                'deal:id,name',
                'owner:id,full_name,email',
                'lineItems:id,invoice_id,product_id,name,description,quantity,unit_price,discount_percent,tax_rate,total,order',
                'payments:id,invoice_id,amount,paid_at,method,reference,recorded_by,notes',
                'payments.recordedBy:id,full_name',
            ])
            ->findOrFail($this->invoiceId);

        return view('invoices::livewire.invoice-detail', [
            'invoice' => $invoice,
        ])->extends('core::layouts.module', ['title' => 'Invoice '.$invoice->number]);
    }
}
