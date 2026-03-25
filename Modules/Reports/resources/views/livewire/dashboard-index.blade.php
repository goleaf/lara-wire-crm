<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="grid gap-4 lg:grid-cols-2 lg:items-end">
            <label class="space-y-1">
                <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Search Dashboards</span>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Find by name..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
                >
            </label>
            @can('reports.create')
                <form wire:submit="createDashboard" class="grid gap-3 md:grid-cols-[1fr_auto]">
                    <label class="space-y-1">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Dashboard Name</span>
                        <input wire:model="name" type="text" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @error('name')
                            <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                        @enderror
                    </label>
                    <div class="flex items-center gap-3 self-end">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                            <input type="checkbox" wire:model="isPublic">
                            Public
                        </label>
                        <button type="submit" class="crm-btn crm-btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            @endcan
        </div>
    </x-crm.card>

    <x-crm.card class="p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Owner</th>
                        <th class="px-3 py-2">Visibility</th>
                        <th class="px-3 py-2">Default</th>
                        <th class="px-3 py-2">Updated</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($dashboards as $dashboard)
                        <tr wire:key="dashboard-row-{{ $dashboard->id }}">
                            <td class="px-3 py-2 font-medium text-slate-900 dark:text-slate-100">{{ $dashboard->name }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->owner?->full_name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->is_public ? 'Public' : 'Private' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->is_default ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $dashboard->updated_at?->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-right">
                                @can('reports.edit')
                                    <a href="{{ route('dashboards.edit', $dashboard->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">
                                        Edit Layout
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No dashboards yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-crm.card>

    <div>
        {{ $dashboards->links() }}
    </div>
</section>
