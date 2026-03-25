<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Reports</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Build reusable analytics and add them to dashboards.</p>
            </div>
            <a href="{{ route('reports.create') }}" wire:navigate class="crm-btn crm-btn-primary">
                New Report
            </a>
        </div>
    </x-crm.card>

    <article class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Module</span>
            <select wire:model.live="moduleFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($modules as $module)
                    <option value="{{ $module }}">{{ $module }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Type</span>
            <select wire:model.live="typeFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Owner</span>
            <select wire:model.live="ownerFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
        </label>
    </article>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Module</th>
                    <th class="px-3 py-2">Type</th>
                    <th class="px-3 py-2">Owner</th>
                    <th class="px-3 py-2">Visibility</th>
                    <th class="px-3 py-2">Created</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($reports as $report)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('reports.show', $report->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                {{ $report->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $report->module }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $report->type }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $report->owner?->full_name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $report->is_public ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200' }}">
                                {{ $report->is_public ? 'Public' : 'Private' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $report->created_at?->format('Y-m-d') }}</td>
                        <td class="px-3 py-2">
                            <div class="flex flex-wrap justify-end gap-1">
                                <a href="{{ route('reports.show', $report->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Run</a>
                                <a href="{{ route('reports.edit', $report->id) }}" wire:navigate class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">Edit</a>
                                <a href="{{ route('reports.export', $report->id) }}" class="rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700">CSV</a>
                                <button type="button" wire:click="deleteReport('{{ $report->id }}')" class="rounded border border-rose-300 px-2 py-1 text-xs text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $reports->links() }}
    </div>
</section>
