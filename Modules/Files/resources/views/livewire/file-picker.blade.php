<section class="space-y-4">
    <div class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex items-center justify-between gap-3">
            <h4 class="text-base font-semibold text-slate-900 dark:text-white">Pick Existing Files</h4>
            <button wire:click="confirmSelection" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                Confirm Selection
            </button>
        </div>
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search file name..."
            class="mt-4 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
        />
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($files as $file)
            <button
                wire:click="toggleSelection('{{ $file->id }}')"
                class="rounded-2xl border p-4 text-left transition {{ in_array($file->id, $selectedIds, true) ? 'border-sky-400 bg-sky-50 dark:border-sky-400/50 dark:bg-sky-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600' }}"
            >
                <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $file->name }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ strtoupper($file->extension) }} • {{ $file->size_formatted }}</p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $file->created_at?->diffForHumans() }}</p>
            </button>
        @empty
            <p class="col-span-full py-6 text-center text-sm text-slate-500 dark:text-slate-400">No files match your search.</p>
        @endforelse
    </div>

    {{ $files->links() }}
</section>
