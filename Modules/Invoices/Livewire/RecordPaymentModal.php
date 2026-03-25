<?php

namespace Modules\Invoices\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Invoices\Models\Invoice;

#[Defer]
class RecordPaymentModal extends Component
{
    public string $invoiceId = '';

    public string $amount = '0';

    public string $paid_at = '';

    public string $method = 'Bank Transfer';

    public string $reference = '';

    public string $notes = '';

    public function mount(string $invoiceId): void
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        $this->invoiceId = $invoiceId;
        $this->hydrateDefaults();
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        $invoice = Invoice::query()
            ->select(['id', 'total', 'amount_paid'])
            ->findOrFail($this->invoiceId);

        $validated = $this->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:'.$invoice->balance_due],
            'paid_at' => ['required', 'date'],
            'method' => ['required', Rule::in(['Bank Transfer', 'Cash', 'Cheque', 'Internal Credit'])],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice->recordPayment($validated + [
            'recorded_by' => (string) auth()->id(),
        ]);

        $this->hydrateDefaults();
        $this->dispatch('payment-recorded');
        session()->flash('status', 'Payment recorded.');
    }

    public function render(): View
    {
        $invoice = Invoice::query()
            ->select(['id', 'total', 'amount_paid', 'currency'])
            ->findOrFail($this->invoiceId);

        return view('invoices::livewire.record-payment-modal', [
            'balanceDue' => $invoice->balance_due,
            'currency' => $invoice->currency,
        ]);
    }

    protected function hydrateDefaults(): void
    {
        $invoice = Invoice::query()
            ->select(['id', 'total', 'amount_paid'])
            ->findOrFail($this->invoiceId);

        $this->amount = (string) $invoice->balance_due;
        $this->paid_at = now()->toDateString();
        $this->method = 'Bank Transfer';
        $this->reference = '';
        $this->notes = '';
    }
}
