<section class="space-y-6">
    <x-crm.card class="p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Support Dashboard</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Operational view for open load, SLA risk, and team quality.</p>
    </x-crm.card>

    <div class="grid gap-3 md:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Open Cases</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $openCasesCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Overdue</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700 dark:text-rose-300">{{ $overdueCasesCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Avg Resolution (hours)</p>
            <p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-300">{{ number_format($averageResolutionHours, 2) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Avg CSAT</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ number_format($averageCsat, 2) }}/5</p>
        </article>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-crm.card class="p-6 lg:col-span-1">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Cases By Priority</h4>
            <div class="mt-4 space-y-3">
                @php
                    $priorityTotal = collect($priorities)->sum('count');
                @endphp
                @foreach ($priorities as $priority)
                    @php
                        $width = $priorityTotal > 0 ? round(($priority['count'] / $priorityTotal) * 100, 2) : 0;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="text-slate-600 dark:text-slate-300">{{ $priority['name'] }}</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $priority['count'] }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2 rounded-full {{ $priority['color'] }}" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-crm.card>

        <x-crm.card class="p-6 lg:col-span-1">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">My Open Cases</h4>
            <div class="mt-4 space-y-2">
                @forelse ($myOpenCases as $supportCase)
                    <a href="{{ route('cases.show', $supportCase->id) }}" wire:navigate class="block rounded-xl border border-slate-200 px-3 py-2 text-sm hover:border-sky-300 dark:border-slate-700">
                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $supportCase->number }}</p>
                        <p class="text-slate-700 dark:text-slate-300">{{ $supportCase->title }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $supportCase->priority }} • {{ $supportCase->status }}</p>
                    </a>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No open cases assigned.</p>
                @endforelse
            </div>
        </x-crm.card>

        <article class="rounded-3xl border border-rose-200 bg-rose-50/80 p-6 shadow-sm lg:col-span-1 dark:border-rose-500/30 dark:bg-rose-500/10">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-700 dark:text-rose-300">SLA Breach Alerts</h4>
            <div class="mt-4 space-y-2">
                @forelse ($slaBreaches as $supportCase)
                    <a href="{{ route('cases.show', $supportCase->id) }}" wire:navigate class="block rounded-xl border border-rose-200 bg-white/90 px-3 py-2 text-sm dark:border-rose-500/30 dark:bg-slate-950/60">
                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $supportCase->number }}</p>
                        <p class="text-slate-700 dark:text-slate-300">{{ $supportCase->title }}</p>
                        <p class="text-xs text-rose-700 dark:text-rose-300">
                            {{ $supportCase->owner?->full_name ?? 'Unknown owner' }} • {{ $supportCase->sla_deadline?->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No current SLA breaches.</p>
                @endforelse
            </div>
        </article>
    </div>
</section>
