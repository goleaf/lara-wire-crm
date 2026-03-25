<section class="space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Notification Center</h3>

        <div class="mt-5 grid gap-3 md:grid-cols-4">
            <select wire:model.live="typeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>

            <select wire:model.live="readFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Any status</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>

            <input wire:model.live="dateFrom" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.live="dateTo" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="markSelectedRead"
                class="rounded-xl border border-sky-300 px-3 py-1.5 text-xs font-semibold text-sky-700 dark:border-sky-500/40 dark:text-sky-300"
            >
                Mark selected read
            </button>
            <button
                type="button"
                wire:click="deleteSelected"
                class="rounded-xl border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
            >
                Delete selected
            </button>
        </div>
    </article>

    <article class="crm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3"></th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Body</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($notifications as $notification)
                        <tr class="{{ $notification->is_read ? 'border-l-4 border-slate-300 dark:border-slate-700' : 'border-l-4 border-sky-400' }} odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:change="toggleSelection('{{ $notification->id }}')" @checked(in_array($notification->id, $selected, true)) class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-500/20 dark:text-slate-300">
                                    {{ $notification->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium {{ $notification->is_read ? 'text-slate-700 dark:text-slate-300' : 'text-slate-900 dark:text-slate-100' }}">
                                {{ $notification->title }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $notification->body ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $notification->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                @if ($notification->is_read)
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-500/20 dark:text-slate-300">Read</span>
                                @else
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">Unread</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @if (! $notification->is_read)
                                        <button
                                            type="button"
                                            wire:click="markRead('{{ $notification->id }}')"
                                            class="rounded-lg border border-sky-300 px-3 py-1.5 text-xs font-medium text-sky-700 dark:border-sky-500/40 dark:text-sky-300"
                                        >
                                            Mark read
                                        </button>
                                    @endif
                                    @if ($notification->action_url)
                                        <a
                                            href="{{ $notification->action_url }}"
                                            wire:navigate
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300"
                                        >
                                            Open
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $notifications->links() }}
        </div>
    </article>
</section>
