<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $reportId ? 'Edit Report' : 'Create Report' }}</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure source module, chart type, filters, and date range.</p>
    </article>

    <form wire:submit="save" class="space-y-6">
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Name</span>
                    <input wire:model.live.debounce.300ms="name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @error('name')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Description</span>
                    <textarea wire:model.live.debounce.300ms="description" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Module</span>
                    <select wire:model.live="module" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($modules as $moduleOption)
                            <option value="{{ $moduleOption }}">{{ $moduleOption }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Type</span>
                    <select wire:model.live="type" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($types as $typeOption)
                            <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Group By</span>
                    <input wire:model.live.debounce.300ms="group_by" type="text" placeholder="e.g. status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Metrics (comma-separated)</span>
                    <input wire:model.live.debounce.300ms="metrics" type="text" placeholder="count,sum:amount" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Date Field</span>
                    <input wire:model.live.debounce.300ms="date_field" type="text" placeholder="created_at" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Date Range</span>
                    <select wire:model.live="date_range" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($dateRanges as $range)
                            <option value="{{ $range }}">{{ $range }}</option>
                        @endforeach
                    </select>
                </label>

                @if ($date_range === 'Custom')
                    <label class="space-y-1">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Custom Date From</span>
                        <input wire:model.live="custom_date_from" type="date" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    </label>
                    <label class="space-y-1">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Custom Date To</span>
                        <input wire:model.live="custom_date_to" type="date" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    </label>
                @endif

                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Filters (JSON object)</span>
                    <textarea wire:model.live.debounce.500ms="filters_json" rows="5" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-xs dark:border-slate-700 dark:bg-slate-900"></textarea>
                    @error('filters_json')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>

                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                    <input type="checkbox" wire:model.live="is_public">
                    Public report
                </label>
            </div>
        </article>

        @if ($previewConfig)
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Live Preview</h4>
                <div class="mt-4 h-72">
                    <x-reports::chart :chart-id="'report-builder-preview'" :config="$previewConfig" />
                </div>
            </article>
        @endif

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('reports.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">
                Save Report
            </button>
        </div>
    </form>
</section>
