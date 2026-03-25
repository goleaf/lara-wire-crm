<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Teams</h3>
            <a href="{{ route('teams.create') }}" wire:navigate class="crm-btn crm-btn-primary">
                New Team
            </a>
        </div>
    </x-crm.card>

    <x-crm.card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Manager</th>
                        <th class="px-4 py-3">Region</th>
                        <th class="px-4 py-3">Members</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($teams as $team)
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $team->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $team->manager?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $team->region ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $team->members_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('teams.edit', $team->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        Edit
                                    </a>
                                    <button
                                        wire:click="delete('{{ $team->id }}')"
                                        onclick="return confirm('Delete this team?')"
                                        class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                No teams found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $teams->links() }}
        </div>
    </x-crm.card>
</section>

