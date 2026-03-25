<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Quotes</h1>
        <a href="{{ route('quotes.create') }}" wire:navigate class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
            New Quote
        </a>
    </div>

    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
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
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Owner</span>
            <select wire:model.live="ownerFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
        </label>

        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Expired</span>
            <select wire:model.live="expiredFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                <option value="1">Expired only</option>
                <option value="0">Not expired</option>
            </select>
        </label>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Number</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Account</th>
                    <th class="px-3 py-2">Deal</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Total</th>
                    <th class="px-3 py-2">Valid Until</th>
                    <th class="px-3 py-2">Owner</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($quotes as $quote)
                    @php
                        $statusClasses = match ($quote->status) {
                            'Draft' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                            'Sent' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                            'Accepted' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
                            'Rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
                            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                        };
                    @endphp
                    <tr class="{{ $quote->is_expired ? 'bg-amber-50/70 dark:bg-amber-900/10' : '' }}">
                        <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $quote->number }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route('quotes.show', $quote->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                {{ $quote->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ $quote->account?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ $quote->deal?->name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClasses }}">{{ $quote->status }}</span>
                                <select
                                    wire:change="changeStatus('{{ $quote->id }}', $event.target.value)"
                                    class="rounded border border-slate-300 px-2 py-0.5 text-xs dark:border-slate-700 dark:bg-slate-900"
                                >
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($quote->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="px-3 py-2 font-semibold text-slate-800 dark:text-slate-100">
                            {{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}
                        </td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">
                            {{ $quote->valid_until?->toDateString() ?? '—' }}
                            @if ($quote->is_expired)
                                <span class="ml-1 rounded bg-amber-200 px-1.5 py-0.5 text-[10px] font-semibold text-amber-900 dark:bg-amber-800 dark:text-amber-100">Expired</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ $quote->owner?->full_name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap justify-end gap-1">
                                <a href="{{ route('quotes.show', $quote->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">View</a>
                                <a href="{{ route('quotes.edit', $quote->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Edit</a>
                                <form method="POST" action="{{ route('quotes.duplicate', $quote->id) }}">
                                    @csrf
                                    <button class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Duplicate</button>
                                </form>
                                <form method="POST" action="{{ route('quotes.convert', $quote->id) }}">
                                    @csrf
                                    <button class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Convert</button>
                                </form>
                                <a href="{{ route('quotes.pdf', $quote->id) }}" class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">PDF</a>
                                <button
                                    type="button"
                                    wire:click="delete('{{ $quote->id }}')"
                                    class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No quotes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $quotes->links() }}
    </div>
</div>
