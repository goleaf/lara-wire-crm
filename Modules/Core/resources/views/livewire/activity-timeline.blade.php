<section class="space-y-3">
    <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Timeline</h4>
    @forelse ($items as $item)
        @php
            $leftBorder = match ($item['type']) {
                'activity' => 'border-sky-300 dark:border-sky-500/30',
                'comment' => 'border-amber-300 dark:border-amber-500/30',
                'file' => 'border-violet-300 dark:border-violet-500/30',
                default => 'border-slate-300 dark:border-slate-700',
            };
        @endphp
        <article class="rounded-2xl border-l-4 {{ $leftBorder }} border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $item['title'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Carbon::parse($item['date'])->format('Y-m-d H:i') }}</p>
            </div>
            @if ($item['body'] !== '')
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $item['body'] }}</p>
            @endif
        </article>
    @empty
        <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
            No timeline records yet.
        </p>
    @endforelse
</section>
