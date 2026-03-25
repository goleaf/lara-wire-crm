<section class="space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Support Cases</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track status, priorities, and SLA performance.</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="setViewMode('table')"
                    class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $viewMode === 'table' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}"
                >
                    Table
                </button>
                <button
                    type="button"
                    wire:click="setViewMode('kanban')"
                    class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $viewMode === 'kanban' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}"
                >
                    Kanban
                </button>
                <a href="{{ route('cases.create') }}" wire:navigate class="crm-btn crm-btn-primary">
                    New Case
                </a>
            </div>
        </div>
    </article>

    <div class="grid gap-3 md:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Open</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $summary['open'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">In Progress</p>
            <p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-300">{{ $summary['in_progress'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Pending</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Resolved Today</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['resolved_today'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">SLA Breached</p>
            <p class="mt-2 text-2xl font-semibold text-rose-700 dark:text-rose-300">{{ $summary['sla_breached'] }}</p>
        </div>
    </div>

    <article class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-6 dark:border-slate-800 dark:bg-slate-900">
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Status</span>
            <select wire:model.live="statusFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Priority</span>
            <select wire:model.live="priorityFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}">{{ $priority }}</option>
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
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Account</span>
            <select wire:model.live="accountFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Overdue</span>
            <select wire:model.live="overdueFilter" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All</option>
                <option value="1">Breached only</option>
                <option value="0">Not breached</option>
            </select>
        </label>
    </article>

    @if ($viewMode === 'kanban')
        <div class="grid gap-4 xl:grid-cols-5">
            @foreach ($statuses as $status)
                <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-3 flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $status }}</h4>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            {{ $kanbanCases->get($status, collect())->count() }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        @forelse ($kanbanCases->get($status, collect()) as $supportCase)
                            <a href="{{ route('cases.show', $supportCase->id) }}" wire:navigate class="block rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm hover:border-sky-300 hover:shadow-sm dark:border-slate-700 dark:bg-slate-950/40">
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $supportCase->number }}</p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $supportCase->title }}</p>
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $supportCase->priority }} • {{ $supportCase->owner?->full_name ?? '—' }}</p>
                            </a>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-300 px-3 py-4 text-center text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">No cases</p>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-3 py-2">Number</th>
                        <th class="px-3 py-2">Title</th>
                        <th class="px-3 py-2">Account</th>
                        <th class="px-3 py-2">Contact</th>
                        <th class="px-3 py-2">Priority</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Owner</th>
                        <th class="px-3 py-2">SLA Deadline</th>
                        <th class="px-3 py-2">CSAT</th>
                        <th class="px-3 py-2">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($cases as $supportCase)
                        @php
                            $priorityClass = match ($supportCase->priority) {
                                'Critical' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
                                'High' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200',
                                'Medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                            };
                            $statusClass = match ($supportCase->status) {
                                'Open' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                                'In Progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200',
                                'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                                'Resolved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
                                default => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                            };
                            $rowClass = match ($supportCase->priority) {
                                'Critical' => 'bg-rose-50/70 dark:bg-rose-900/10',
                                'High' => 'bg-orange-50/70 dark:bg-orange-900/10',
                                default => '',
                            };
                            $slaClass = 'text-slate-600 dark:text-slate-300';
                            if ($supportCase->is_overdue) {
                                $slaClass = 'text-rose-700 dark:text-rose-300';
                            } elseif ($supportCase->sla_deadline !== null) {
                                $totalMinutes = max((int) $supportCase->created_at?->diffInMinutes($supportCase->sla_deadline), 1);
                                $remainingMinutes = (int) now()->diffInMinutes($supportCase->sla_deadline, false);
                                $remainingPercent = ($remainingMinutes / $totalMinutes) * 100;
                                $slaClass = $remainingPercent > 50
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : ($remainingPercent > 20 ? 'text-amber-700 dark:text-amber-300' : 'text-rose-700 dark:text-rose-300');
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $supportCase->number }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('cases.show', $supportCase->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                    {{ $supportCase->title }}
                                </a>
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $supportCase->account?->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $supportCase->contact?->full_name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $priorityClass }}">{{ $supportCase->priority }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $supportCase->status }}</span>
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $supportCase->owner?->full_name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                @if ($supportCase->is_overdue)
                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-200">BREACHED</span>
                                @elseif ($supportCase->sla_deadline)
                                    <p class="text-xs {{ $slaClass }}">{{ $supportCase->sla_deadline->format('Y-m-d H:i') }}</p>
                                    <p class="text-xs {{ $slaClass }}">{{ $supportCase->sla_deadline->diffForHumans() }}</p>
                                @else
                                    <span class="text-xs text-slate-500 dark:text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">
                                {{ $supportCase->satisfaction_score ? $supportCase->satisfaction_score.'/5' : '—' }}
                            </td>
                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $supportCase->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No cases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            {{ $cases->links() }}
        </div>
    @endif
</section>
