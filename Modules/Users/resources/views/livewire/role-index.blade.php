<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Roles</h3>
            <a href="{{ route('roles.create') }}" wire:navigate class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                New Role
            </a>
        </div>
    </article>

    <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Visibility</th>
                        <th class="px-4 py-3">View</th>
                        <th class="px-4 py-3">Create</th>
                        <th class="px-4 py-3">Edit</th>
                        <th class="px-4 py-3">Delete</th>
                        <th class="px-4 py-3">Export</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($roles as $role)
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $role->name }}</td>
                            <td class="px-4 py-3">{{ ucfirst($role->record_visibility) }}</td>
                            @foreach (['can_view', 'can_create', 'can_edit', 'can_delete', 'can_export'] as $permission)
                                <td class="px-4 py-3">
                                    <input
                                        type="checkbox"
                                        @checked($role->{$permission})
                                        wire:change="updatePermission('{{ $role->id }}', '{{ $permission }}', $event.target.checked)"
                                        class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                    />
                                </td>
                            @endforeach
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('roles.edit', $role->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                No roles defined.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

