<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Users</h3>
            <a
                href="{{ route('users.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500"
            >
                New User
            </a>
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-4">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search name or email"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
            />

            <select wire:model.live="roleFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="teamFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Teams</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="activeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </article>

    <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Avatar</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Team</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Last Login</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($users as $user)
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3">
                                @if ($user->avatar_path)
                                    <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="{{ $user->full_name }}" class="size-10 rounded-full object-cover" />
                                @else
                                    <div class="inline-flex size-10 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white dark:bg-slate-200 dark:text-slate-900">
                                        {{ $user->initials() }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $user->full_name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $roleName = $user->role?->name ?? 'Unassigned';
                                    $roleClass = match (strtolower($roleName)) {
                                        'admin' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                        'manager' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                        'sales rep' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleClass }}">{{ $roleName }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $user->team?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <button
                                    wire:click="toggleActive('{{ $user->id }}')"
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' }}"
                                >
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                                {{ $user->last_login?->diffForHumans() ?? 'Never' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        Edit
                                    </a>
                                    <button
                                        wire:click="delete('{{ $user->id }}')"
                                        onclick="return confirm('Delete this user?')"
                                        class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $users->links() }}
        </div>
    </article>
</section>

