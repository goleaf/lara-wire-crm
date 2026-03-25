<div>
    @if (auth()->user()?->can('notifications.view'))
        <div class="relative" wire:poll.30s="refreshCount">
            <button
                type="button"
                wire:click="toggle"
                class="relative inline-flex size-11 items-center justify-center rounded-2xl border border-white/70 bg-white/80 text-slate-600 shadow-sm transition hover:text-sky-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:text-sky-200"
                aria-label="Notifications"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17H5.143A1.143 1.143 0 0 1 4 15.857V14.43a3.429 3.429 0 0 1 1.004-2.425l.425-.426A2.286 2.286 0 0 0 6.286 9.96V8.857a5.714 5.714 0 1 1 11.428 0V9.96c0 .607.241 1.19.67 1.62l.425.425A3.429 3.429 0 0 1 20 14.43v1.428A1.143 1.143 0 0 1 18.857 17h-4Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.714 17a2.286 2.286 0 1 0 4.572 0" />
                </svg>
                @if ($unreadCount > 0)
                    <span class="absolute right-2 top-2 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold text-white">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    <span class="absolute right-1.5 top-1.5 size-2.5 animate-pulse rounded-full bg-rose-500"></span>
                @endif
            </button>

            @if ($open)
                <div class="absolute right-0 top-14 z-50 w-[22rem]">
                    @livewire(\Modules\Notifications\Livewire\NotificationDropdown::class, [], key('notifications-dropdown-'.auth()->id()))
                </div>
            @endif
        </div>
    @endif
</div>
