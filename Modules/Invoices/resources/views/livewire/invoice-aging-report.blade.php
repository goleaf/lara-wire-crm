<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Invoice Aging Report</h1>
        <a href="{{ route('invoices.index') }}" wire:navigate class="rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Back to Invoices</a>
    </div>

    @php
        $labels = [
            'current' => 'Current',
            '1_30' => '1-30 days overdue',
            '31_60' => '31-60 days overdue',
            '61_90' => '61-90 days overdue',
            '90_plus' => '90+ days overdue',
        ];
    @endphp

    <div class="grid gap-3 md:grid-cols-5">
        @foreach ($labels as $bucket => $label)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</p>
                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $summary[$bucket]['count'] }} invoices</p>
                <p class="text-sm {{ $bucket === 'current' ? 'text-slate-700 dark:text-slate-300' : 'text-rose-700 dark:text-rose-300' }}">
                    {{ number_format($summary[$bucket]['value'], 2) }}
                </p>
            </div>
        @endforeach
    </div>

    @foreach ($labels as $bucket => $label)
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $label }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                        <tr>
                            <th class="px-3 py-2">Invoice</th>
                            <th class="px-3 py-2">Account</th>
                            <th class="px-3 py-2">Owner</th>
                            <th class="px-3 py-2">Due Date</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-right">Balance Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($bucketed[$bucket] as $invoice)
                            <tr>
                                <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" wire:navigate class="hover:text-sky-600 dark:hover:text-sky-300">
                                        {{ $invoice->number }}
                                    </a>
                                </td>
                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->account?->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->owner?->full_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->due_date?->toDateString() }}</td>
                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->status }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-slate-900 dark:text-slate-100">{{ number_format($invoice->balance_due, 2) }} {{ $invoice->currency }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-slate-500 dark:text-slate-400">No invoices in this bucket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
