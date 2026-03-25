<article class="overflow-hidden rounded-3xl border border-white/70 bg-white/95 shadow-xl backdrop-blur dark:border-white/10 dark:bg-slate-950/95">
    <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800">
        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Notifications</h4>
        <button
            type="button"
            wire:click="markAllRead"
            class="text-xs font-medium text-sky-600 hover:text-sky-500 dark:text-sky-300"
        >
            Mark all read
        </button>
    </header>

    <div class="max-h-96 overflow-y-auto">
        @forelse ($notifications as $notification)
            @php
                $typeClass = match ($notification->type) {
                    'Reminder' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                    'Mention' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                    'Assignment' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-300',
                    'SLA Breach' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                    'Payment Recorded' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
                };
            @endphp
            <button
                type="button"
                wire:click="markRead('{{ $notification->id }}')"
                class="block w-full border-b border-slate-200 px-4 py-3 text-left transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900/80"
            >
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $typeClass }}">
                        {{ $notification->type }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="truncate text-sm font-semibold {{ $notification->is_read ? 'text-slate-700 dark:text-slate-300' : 'text-slate-900 dark:text-slate-100' }}">
                                {{ $notification->title }}
                            </p>
                            @unless ($notification->is_read)
                                <span class="mt-1 size-2 shrink-0 rounded-full bg-sky-500"></span>
                            @endunless
                        </div>
                        @if ($notification->body)
                            <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $notification->body }}</p>
                        @endif
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </button>
        @empty
            <p class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No notifications yet.</p>
        @endforelse
    </div>

    <footer class="border-t border-slate-200 px-4 py-3 text-right dark:border-slate-800">
        <a
            href="{{ route('notifications.index') }}"
            wire:navigate
            class="text-sm font-medium text-sky-600 hover:text-sky-500 dark:text-sky-300"
        >
            View all
        </a>
    </footer>
</article>
