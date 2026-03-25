<article class="rounded-3xl border border-amber-200 bg-amber-50/80 p-6 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Case Satisfaction</h3>
    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Capture final customer satisfaction (1-5 stars).</p>

    <div class="mt-4 flex items-center gap-2">
        @for ($value = 1; $value <= 5; $value++)
            <button
                type="button"
                wire:click="setScore({{ $value }})"
                class="text-2xl leading-none {{ $score >= $value ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600' }}"
                aria-label="Rate {{ $value }} stars"
            >
                ★
            </button>
        @endfor
    </div>
    @error('score')
        <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
    @enderror

    <label class="mt-4 block space-y-1">
        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Comment</span>
        <textarea wire:model.live.debounce.300ms="comment" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
    </label>
    @error('comment')
        <p class="mt-2 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
    @enderror

    <div class="mt-4 flex justify-end">
        <button type="button" wire:click="save" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-400">
            Save Satisfaction
        </button>
    </div>
</article>
