<div>
    @if ($open)
        <div class="fixed inset-0 z-[96] flex items-center justify-center bg-slate-950/60 p-4">
            <div class="w-full max-w-md rounded-3xl border border-white/20 bg-white p-6 shadow-2xl dark:bg-slate-950">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Confirm Action</h3>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $message }}</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirm" class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-500">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
