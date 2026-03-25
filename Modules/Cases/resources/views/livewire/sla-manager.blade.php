<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">SLA Manager</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">One policy per priority, with editable first response and resolution windows.</p>
    </article>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                <tr>
                    <th class="px-3 py-2">Priority</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">First Response (hours)</th>
                    <th class="px-3 py-2">Resolution (hours)</th>
                    <th class="px-3 py-2">Active</th>
                    <th class="px-3 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($policiesList as $policy)
                    @php
                        $priorityClass = match ($policy->priority) {
                            'Critical' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
                            'High' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200',
                            'Medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                        };
                    @endphp
                    <tr>
                        <td class="px-3 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $priorityClass }}">{{ $policy->priority }}</span>
                        </td>
                        <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $policy->name }}</td>
                        <td class="px-3 py-2">
                            <input type="number" min="1" max="240" wire:model.live="policies.{{ $policy->id }}.first_response_hours" class="w-28 rounded-md border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" min="1" max="1000" wire:model.live="policies.{{ $policy->id }}.resolution_hours" class="w-28 rounded-md border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900">
                        </td>
                        <td class="px-3 py-2">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                <input type="checkbox" wire:model.live="policies.{{ $policy->id }}.is_active">
                                Enabled
                            </label>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <button
                                type="button"
                                wire:click="savePolicy('{{ $policy->id }}')"
                                class="rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-500"
                            >
                                Save
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-500 dark:text-slate-400">No SLA policies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
