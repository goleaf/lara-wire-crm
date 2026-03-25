<section class="mx-auto max-w-4xl space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Notification Preferences</h3>

        <form wire:submit="save" class="mt-6 space-y-6">
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($types as $type => $enabled)
                    <label class="inline-flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $type }}</span>
                        <input wire:model.live="types.{{ $type }}" type="checkbox" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                    </label>
                @endforeach
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Quiet hours start</label>
                    <input wire:model.live="quiet_hours_start" type="time" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('quiet_hours_start') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Quiet hours end</label>
                    <input wire:model.live="quiet_hours_end" type="time" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('quiet_hours_end') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                    Save Preferences
                </button>
            </div>
        </form>
    </article>
</section>
