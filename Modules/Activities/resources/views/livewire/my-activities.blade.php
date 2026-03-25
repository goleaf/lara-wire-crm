<section class="space-y-6">
    <article class="crm-card p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">My Activities</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Overdue, due today, and upcoming in the next 7 days.</p>
            </div>
            <a href="{{ route('activities.create') }}" wire:navigate class="crm-btn crm-btn-primary">
                New Activity
            </a>
        </div>
    </article>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-rose-200 bg-rose-50/80 p-5 shadow-sm dark:border-rose-500/30 dark:bg-rose-500/10">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-700 dark:text-rose-300">Overdue</h4>
                <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">{{ $overdue->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse ($overdue as $activity)
                    <label class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-white/90 px-3 py-2 text-sm dark:border-rose-500/30 dark:bg-slate-950/40">
                        <input type="checkbox" wire:click="markDone('{{ $activity->id }}', true)" class="mt-1" />
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->type }} • {{ $activity->due_date?->format('Y-m-d H:i') }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No overdue activities.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-amber-200 bg-amber-50/80 p-5 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700 dark:text-amber-300">Today</h4>
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">{{ $today->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse ($today as $activity)
                    <label class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-white/90 px-3 py-2 text-sm dark:border-amber-500/30 dark:bg-slate-950/40">
                        <input type="checkbox" wire:click="markDone('{{ $activity->id }}', true)" class="mt-1" />
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->type }} • {{ $activity->due_date?->format('H:i') }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Nothing scheduled today.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-3xl border border-sky-200 bg-sky-50/80 p-5 shadow-sm dark:border-sky-500/30 dark:bg-sky-500/10">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700 dark:text-sky-300">Upcoming</h4>
                <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/20 dark:text-sky-300">{{ $upcoming->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse ($upcoming as $activity)
                    <label class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-white/90 px-3 py-2 text-sm dark:border-sky-500/30 dark:bg-slate-950/40">
                        <input type="checkbox" wire:click="markDone('{{ $activity->id }}', true)" class="mt-1" />
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->type }} • {{ $activity->due_date?->format('Y-m-d H:i') }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No upcoming activities.</p>
                @endforelse
            </div>
        </article>
    </div>
</section>
