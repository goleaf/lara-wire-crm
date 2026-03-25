<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Invoice</p>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $invoice->number }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Due {{ $invoice->due_date?->toFormattedDateString() }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @php
                $statusClasses = match ($invoice->status) {
                    'Draft' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                    'Issued' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                    'Partially Paid' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                    'Paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
                    'Overdue' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
                    default => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                };
            @endphp
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $invoice->status }}</span>
            <a href="{{ route('invoices.edit', $invoice->id) }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Edit</a>
            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Download PDF</a>
            <button type="button" wire:click="cancelInvoice" class="rounded-md border border-rose-300 px-3 py-2 text-sm text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                Cancel
            </button>
        </div>
    </div>

    @if ($invoice->is_overdue)
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800/60 dark:bg-rose-900/20 dark:text-rose-200">
            This invoice is overdue. Balance due: {{ number_format($invoice->balance_due, 2) }} {{ $invoice->currency }}.
        </div>
    @endif

    @php
        $paymentPercent = $invoice->total > 0 ? min(100, round(($invoice->amount_paid / $invoice->total) * 100, 2)) : 0;
    @endphp
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-2 flex items-center justify-between text-sm">
            <span class="font-medium text-slate-700 dark:text-slate-200">Payment Progress</span>
            <span class="text-slate-600 dark:text-slate-300">{{ number_format($paymentPercent, 2) }}%</span>
        </div>
        <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-700">
            <div class="h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $paymentPercent }}%"></div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Account</p>
            <p class="mt-1 font-medium text-slate-900 dark:text-slate-100">{{ $invoice->account?->name ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Contact</p>
            <p class="mt-1 font-medium text-slate-900 dark:text-slate-100">
                {{ trim(($invoice->contact?->first_name ?? '').' '.($invoice->contact?->last_name ?? '')) ?: '—' }}
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Owner</p>
            <p class="mt-1 font-medium text-slate-900 dark:text-slate-100">{{ $invoice->owner?->full_name ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Deal</p>
            <p class="mt-1 font-medium text-slate-900 dark:text-slate-100">{{ $invoice->deal?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Item</th>
                    <th class="px-3 py-2">Qty</th>
                    <th class="px-3 py-2">Unit</th>
                    <th class="px-3 py-2">Discount %</th>
                    <th class="px-3 py-2">Tax %</th>
                    <th class="px-3 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($invoice->lineItems as $lineItem)
                    <tr>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $lineItem->name }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ number_format((float) $lineItem->quantity, 2) }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ number_format((float) $lineItem->unit_price, 2) }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ number_format((float) $lineItem->discount_percent, 2) }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ number_format((float) $lineItem->tax_rate, 2) }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $lineItem->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No line items yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Totals</h2>
            <dl class="space-y-2">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Subtotal</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $invoice->subtotal, 2) }} {{ $invoice->currency }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Discount</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">-{{ number_format((float) $invoice->discount_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500 dark:text-slate-400">Tax</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $invoice->tax_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="font-semibold text-slate-700 dark:text-slate-200">Total</dt>
                    <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $invoice->total, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-emerald-700 dark:text-emerald-300">Amount Paid</dt>
                    <dd class="font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format((float) $invoice->amount_paid, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-200 pt-2 dark:border-slate-700">
                    <dt class="font-semibold {{ $invoice->is_overdue ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200' }}">Balance Due</dt>
                    <dd class="font-bold {{ $invoice->is_overdue ? 'text-rose-700 dark:text-rose-300' : 'text-slate-900 dark:text-slate-100' }}">{{ number_format($invoice->balance_due, 2) }} {{ $invoice->currency }}</dd>
                </div>
            </dl>
        </div>

        @livewire(\Modules\Invoices\Livewire\RecordPaymentModal::class, ['invoiceId' => (string) $invoice->id], key('record-payment-'.$invoice->id))
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Payments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Paid At</th>
                        <th class="px-3 py-2">Amount</th>
                        <th class="px-3 py-2">Method</th>
                        <th class="px-3 py-2">Reference</th>
                        <th class="px-3 py-2">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($invoice->payments as $payment)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $payment->paid_at?->toDateString() }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $payment->amount, 2) }} {{ $invoice->currency }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $payment->method }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $payment->reference ?: '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $payment->recordedBy?->full_name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-slate-500 dark:text-slate-400">No payments recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
