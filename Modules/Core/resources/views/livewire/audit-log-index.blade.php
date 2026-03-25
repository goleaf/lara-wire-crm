<section class="space-y-6">
    <article class="crm-card p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Audit Logs</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track created, updated, and deleted events across key records.</p>
    </article>

    <article class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-5 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">User</span>
            <select wire:model.live="userFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Model</span>
            <select wire:model.live="modelFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($models as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Action</span>
            <select wire:model.live="actionFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Date From</span>
            <input wire:model.live="dateFrom" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Date To</span>
            <input wire:model.live="dateTo" type="date" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
        </label>
    </article>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Date</th>
                    <th class="px-3 py-2">User</th>
                    <th class="px-3 py-2">Action</th>
                    <th class="px-3 py-2">Model</th>
                    <th class="px-3 py-2">Model ID</th>
                    <th class="px-3 py-2">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $log->user?->full_name ?? 'System' }}</td>
                        <td class="px-3 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $log->action === 'deleted' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200' : ($log->action === 'created' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-200') }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $log->model_type }}</td>
                        <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $log->model_id }}</td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No audit entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $logs->links() }}
    </div>
</section>
