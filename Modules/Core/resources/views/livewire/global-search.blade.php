<div
    x-data
    x-on:keydown.window.prevent.ctrl.k="$wire.openSearch()"
    x-on:keydown.window.escape="$wire.closeSearch()"
    class="w-full max-w-xl"
>
    <button
        type="button"
        wire:click="openSearch"
        class="flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-500 shadow-sm hover:border-sky-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
    >
        <span>Search contacts, accounts, deals, leads, cases, campaigns...</span>
        <span class="rounded border border-slate-300 px-2 py-0.5 text-[11px] font-semibold text-slate-500 dark:border-slate-700 dark:text-slate-400">Ctrl+K</span>
    </button>

    @if ($open)
        <div class="fixed inset-0 z-[90] flex items-start justify-center bg-slate-950/55 p-4 pt-16" wire:click="closeSearch">
            <div class="w-full max-w-3xl rounded-3xl border border-white/20 bg-white p-4 shadow-2xl dark:bg-slate-950" wire:click.stop>
                <div class="flex items-center gap-2">
                    <input
                        wire:model.live.debounce.300ms="query"
                        type="text"
                        autofocus
                        placeholder="Type to search..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
                    >
                    <button type="button" wire:click="closeSearch" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium dark:border-slate-700">
                        Esc
                    </button>
                </div>

                <div class="mt-4 max-h-[60vh] space-y-4 overflow-y-auto pr-1">
                    @forelse ($results as $group => $items)
                        <section>
                            <h4 class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ $group }}</h4>
                            <div class="space-y-2">
                                @foreach ($items as $item)
                                    <a href="{{ $item['url'] }}" wire:navigate wire:click="closeSearch" class="block rounded-xl border border-slate-200 px-3 py-2 hover:border-sky-300 dark:border-slate-700">
                                        <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $item['title'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item['subtitle'] }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ mb_strlen(trim($query)) < 2 ? 'Start typing at least 2 characters.' : 'No results found.' }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
