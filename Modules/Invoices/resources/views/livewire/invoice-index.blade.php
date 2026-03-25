<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Invoices</h1>
        <a href="{{ route('invoices.create') }}" wire:navigate class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
            New Invoice
        </a>
    </div>

    <div class="grid gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Issued</p>
            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-slate-100">{{ number_format($summary['issued'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Paid</p>
            <p class="mt-2 text-xl font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($summary['paid'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Overdue</p>
            <p class="mt-2 text-xl font-semibold text-rose-700 dark:text-rose-300">{{ number_format($summary['overdue'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Outstanding Balance</p>
            <p class="mt-2 text-xl font-semibold text-amber-700 dark:text-amber-300">{{ number_format($summary['outstanding'], 2) }}</p>
        </div>
    </div>

    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-5 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Status</span>
            <select wire:model.live="statusFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Account</span>
            <select wire:model.live="accountFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Overdue</span>
            <select wire:model.live="overdueFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                <option value="1">Overdue only</option>
                <option value="0">Not overdue</option>
            </select>
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Issue From</span>
            <input wire:model.live="dateFrom" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Issue To</span>
            <input wire:model.live="dateTo" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Number</th>
                    <th class="px-3 py-2">Account</th>
                    <th class="px-3 py-2">Deal</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Issue Date</th>
                    <th class="px-3 py-2">Due Date</th>
                    <th class="px-3 py-2">Total</th>
                    <th class="px-3 py-2">Paid</th>
                    <th class="px-3 py-2">Balance</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($invoices as $invoice)
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
                    <tr class="{{ $invoice->is_overdue ? 'bg-rose-50/70 dark:bg-rose-900/10' : '' }}">
                        <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $invoice->number }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->account?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->deal?->name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClasses }}">{{ $invoice->status }}</span>
                        </td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->issue_date?->toDateString() }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $invoice->due_date?->toDateString() }}</td>
                        <td class="px-3 py-2 font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</td>
                        <td class="px-3 py-2 text-emerald-700 dark:text-emerald-300">{{ number_format((float) $invoice->amount_paid, 2) }}</td>
                        <td class="px-3 py-2 font-semibold {{ $invoice->is_overdue ? 'text-rose-700 dark:text-rose-300' : 'text-slate-800 dark:text-slate-200' }}">
                            {{ number_format($invoice->balance_due, 2) }}
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap justify-end gap-1">
                                <a href="{{ route('invoices.show', $invoice->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">View</a>
                                <a href="{{ route('invoices.edit', $invoice->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Edit</a>
                                <a href="{{ route('invoices.pdf', $invoice->id) }}" class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">PDF</a>
                                <button
                                    type="button"
                                    wire:click="cancelInvoice('{{ $invoice->id }}')"
                                    class="rounded border border-amber-300 px-2 py-1 text-xs text-amber-700 dark:border-amber-500/40 dark:text-amber-300"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteInvoice('{{ $invoice->id }}')"
                                    class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $invoices->links() }}
    </div>
</div>
