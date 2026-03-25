<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $dashboard->name }}</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update dashboard metadata and persist widget layout JSON.</p>
            </div>
            <a href="{{ route('dashboards.index') }}" wire:navigate class="crm-btn crm-btn-secondary">
                Back to Dashboards
            </a>
        </div>
    </x-crm.card>

    <x-crm.card class="p-6">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Name</span>
                    <input wire:model.blur="name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @error('name')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Layout (JSON)</span>
                    <textarea wire:model.blur="layoutJson" rows="6" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 font-mono text-xs dark:border-slate-700 dark:bg-slate-900"></textarea>
                    @error('layoutJson')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" wire:model="isPublic">
                    Public
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" wire:model="isDefault">
                    Default
                </label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="crm-btn crm-btn-primary">
                    Save Layout
                </button>
            </div>
        </form>
    </x-crm.card>

    <x-crm.card class="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Widget</th>
                        <th class="px-3 py-2">Title</th>
                        <th class="px-3 py-2">Report</th>
                        <th class="px-3 py-2">Position</th>
                        <th class="px-3 py-2">Size</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($dashboard->widgets as $widget)
                        <tr>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->widget_type }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->title ?: '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->report?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->position_x }}, {{ $widget->position_y }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $widget->width }} x {{ $widget->height }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No widgets configured.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-crm.card>
</section>
