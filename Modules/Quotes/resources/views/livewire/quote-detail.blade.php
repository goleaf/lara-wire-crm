<div class="space-y-6">
    @if ($statusMessage || session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-200">
            {{ $statusMessage ?? session('status') }}
        </div>
    @endif

    @php
        $statusClasses = match ($quote->status) {
            'Draft' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
            'Sent' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
            'Accepted' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
            'Rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
        };
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $quote->number }}</p>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $quote->name }}</h1>
                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClasses }}">{{ $quote->status }}</span>
            </div>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                {{ $quote->account?->name ?? 'No account' }}
                @if ($quote->contact)
                    · {{ trim(($quote->contact->first_name ?? '').' '.($quote->contact->last_name ?? '')) }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quotes.edit', $quote->id) }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Edit</a>
            <a href="{{ route('quotes.pdf', $quote->id) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Download PDF</a>
            <form method="POST" action="{{ route('quotes.duplicate', $quote->id) }}">
                @csrf
                <button class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Duplicate</button>
            </form>
            <form method="POST" action="{{ route('quotes.convert', $quote->id) }}">
                @csrf
                <button class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Convert to Invoice</button>
            </form>
            <button type="button" wire:click="markSent" class="rounded-md border border-blue-300 px-3 py-2 text-sm text-blue-700 dark:border-blue-500/40 dark:text-blue-300">Mark Sent</button>
            <button type="button" wire:click="markAccepted" class="rounded-md border border-emerald-300 px-3 py-2 text-sm text-emerald-700 dark:border-emerald-500/40 dark:text-emerald-300">Mark Accepted</button>
            <button type="button" wire:click="markRejected" class="rounded-md border border-rose-300 px-3 py-2 text-sm text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Mark Rejected</button>
        </div>
    </div>

    @if ($quote->is_expired)
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
            This quote is expired.
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">#</th>
                    <th class="px-3 py-2">Description</th>
                    <th class="px-3 py-2">Qty</th>
                    <th class="px-3 py-2">Unit Price</th>
                    <th class="px-3 py-2">Disc%</th>
                    <th class="px-3 py-2">Tax%</th>
                    <th class="px-3 py-2">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($quote->lineItems as $index => $lineItem)
                    <tr class="odd:bg-white even:bg-slate-50/70 dark:odd:bg-slate-950/20 dark:even:bg-slate-900/20">
                        <td class="px-3 py-2 text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-3 py-2">
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $lineItem->name }}</p>
                            @if ($lineItem->description)
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $lineItem->description }}</p>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ number_format((float) $lineItem->quantity, 2) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $lineItem->unit_price, 2) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $lineItem->discount_percent, 2) }}</td>
                        <td class="px-3 py-2">{{ number_format((float) $lineItem->tax_rate, 2) }}</td>
                        <td class="px-3 py-2 font-semibold">{{ number_format((float) $lineItem->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="ml-auto w-full max-w-md rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500 dark:text-slate-400">Subtotal</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $quote->subtotal, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500 dark:text-slate-400">Discount</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">-{{ number_format((float) $quote->discount_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500 dark:text-slate-400">Tax</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">{{ number_format((float) $quote->tax_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between gap-3 border-t border-slate-200 pt-2 text-lg dark:border-slate-700">
                <dt class="font-semibold text-slate-700 dark:text-slate-200">Grand Total</dt>
                <dd class="font-bold text-slate-900 dark:text-white">{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</dd>
            </div>
        </dl>
    </div>

    @if ($quote->notes)
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-2 text-sm font-semibold text-slate-800 dark:text-slate-100">Notes / Terms</h2>
            <p class="whitespace-pre-wrap text-sm text-slate-600 dark:text-slate-300">{{ $quote->notes }}</p>
        </div>
    @endif
</div>
