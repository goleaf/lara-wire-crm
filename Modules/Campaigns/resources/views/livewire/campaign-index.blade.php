<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Campaigns</h1>
        <a href="{{ route('campaigns.create') }}" wire:navigate class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
            New Campaign
        </a>
    </div>

    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-6 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Status</span>
            <select wire:model.live="statusFilter" class="w-full rounded-md border border-slate-300 px-2 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Type</span>
            <select wire:model.live="typeFilter" class="w-full rounded-md border border-slate-300 px-2 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Owner</span>
            <select wire:model.live="ownerFilter" class="w-full rounded-md border border-slate-300 px-2 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Start From</span>
            <input wire:model.live="dateFrom" type="date" class="w-full rounded-md border border-slate-300 px-2 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">End To</span>
            <input wire:model.live="dateTo" type="date" class="w-full rounded-md border border-slate-300 px-2 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
        <div class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">View</span>
            <div class="flex gap-2">
                <button type="button" wire:click="setViewMode('cards')" class="rounded-md px-3 py-2 text-xs {{ $viewMode === 'cards' ? 'bg-sky-600 text-white' : 'border border-slate-300 dark:border-slate-700' }}">Cards</button>
                <button type="button" wire:click="setViewMode('table')" class="rounded-md px-3 py-2 text-xs {{ $viewMode === 'table' ? 'bg-sky-600 text-white' : 'border border-slate-300 dark:border-slate-700' }}">Table</button>
            </div>
        </div>
    </div>

    @if ($viewMode === 'cards')
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($campaigns as $campaign)
                @php
                    $leadProgress = $campaign->expected_leads > 0 ? min(100, round(($campaign->leads_count / $campaign->expected_leads) * 100, 2)) : 0;
                    $budgetProgress = (float) $campaign->budget > 0 ? min(100, round(((float) $campaign->actual_cost / (float) $campaign->budget) * 100, 2)) : 0;
                    $statusClasses = match ($campaign->status) {
                        'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
                        'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                        'Paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                    };
                @endphp
                <a href="{{ route('campaigns.show', $campaign->id) }}" wire:navigate class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $campaign->name }}</h2>
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusClasses }}">{{ $campaign->status }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 dark:bg-slate-800">{{ $campaign->type }}</span>
                        <span>{{ $campaign->start_date?->toDateString() ?? '—' }} → {{ $campaign->end_date?->toDateString() ?? '—' }}</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="mb-1 flex justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Leads progress</span>
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $campaign->leads_count }}/{{ $campaign->expected_leads }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                                <div class="h-2 rounded-full bg-sky-500" style="width: {{ $leadProgress }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 flex justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Budget vs actual</span>
                                <span class="font-medium {{ (float) $campaign->actual_cost > (float) $campaign->budget ? 'text-rose-600 dark:text-rose-300' : 'text-emerald-600 dark:text-emerald-300' }}">
                                    {{ number_format((float) $campaign->actual_cost, 0) }} / {{ number_format((float) $campaign->budget, 0) }}
                                </span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                                <div class="h-2 rounded-full {{ (float) $campaign->actual_cost > (float) $campaign->budget ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $budgetProgress }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-slate-600 dark:text-slate-300">
                        Owner: {{ $campaign->owner?->full_name ?? '—' }}
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                    No campaigns found.
                </div>
            @endforelse
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Dates</th>
                        <th class="px-3 py-2">Leads</th>
                        <th class="px-3 py-2">Budget / Cost</th>
                        <th class="px-3 py-2">ROI%</th>
                        <th class="px-3 py-2">Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($campaigns as $campaign)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('campaigns.show', $campaign->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">{{ $campaign->name }}</a>
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $campaign->type }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $campaign->status }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $campaign->start_date?->toDateString() ?? '—' }} → {{ $campaign->end_date?->toDateString() ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $campaign->leads_count }} / {{ $campaign->expected_leads }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ number_format((float) $campaign->budget, 0) }} / {{ number_format((float) $campaign->actual_cost, 0) }}</td>
                            <td class="px-3 py-2 font-semibold {{ $campaign->roi >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                                {{ number_format($campaign->roi, 2) }}%
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $campaign->owner?->full_name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <div>
        {{ $campaigns->links() }}
    </div>
</div>
