<section class="space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $report->name }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $report->description ?: 'No description provided.' }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="rounded bg-slate-100 px-1.5 py-0.5 dark:bg-slate-800">{{ $report->module }}</span>
                    <span class="rounded bg-slate-100 px-1.5 py-0.5 dark:bg-slate-800">{{ $report->type }}</span>
                    <span>Owner: {{ $report->owner?->full_name ?? '—' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="addToDashboard" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    Add to Dashboard
                </button>
                <a href="{{ route('reports.edit', $report->id) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    Edit
                </a>
                <a href="{{ route('reports.export', $report->id) }}" class="rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-500">
                    Export CSV
                </a>
            </div>
        </div>
    </article>

    @if ($report->type !== 'Table')
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="h-[26rem]">
                <x-reports::chart :chart-id="'report-'.$report->id" :config="$chartConfig" />
            </div>
        </article>
    @else
        <article class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                @php
                    $columns = $tableRows->first() ? array_keys($tableRows->first()->getAttributes()) : [];
                @endphp
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        @forelse ($columns as $column)
                            <th class="px-3 py-2">{{ $column }}</th>
                        @empty
                            <th class="px-3 py-2">No data</th>
                        @endforelse
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($tableRows as $row)
                        <tr>
                            @foreach ($columns as $column)
                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ data_get($row, $column) }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No rows available for the configured filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </article>
    @endif
</section>
