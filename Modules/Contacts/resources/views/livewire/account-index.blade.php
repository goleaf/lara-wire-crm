<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Accounts</h3>
            <div class="flex items-center gap-2">
                <button
                    wire:click="exportCsv"
                    class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300"
                >
                    Export CSV
                </button>
                <a
                    href="{{ route('accounts.create') }}"
                    wire:navigate
                    class="inline-flex items-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500"
                >
                    New Account
                </a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-4">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search name, industry, type"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
            />

            <select wire:model.live="typeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>

            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="industryFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Industries</option>
                @foreach ($industries as $industry)
                    <option value="{{ $industry }}">{{ $industry }}</option>
                @endforeach
            </select>
        </div>
    </article>

    <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800">
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $accounts->total() }} account(s)</p>

            <button
                wire:click="bulkDelete"
                onclick="return confirm('Delete selected accounts?')"
                class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
            >
                Bulk Delete
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Select</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Industry</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Contacts</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($accounts as $account)
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:click="toggleSelection('{{ $account->id }}')" @checked(in_array($account->id, $selected, true)) />
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click="viewAccount('{{ $account->id }}')" class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                    {{ $account->name }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->industry }}</td>
                            <td class="px-4 py-3">
                                <x-contacts::account-badge :account="$account" />
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->owner?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->contacts_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('accounts.edit', $account->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        Edit
                                    </a>
                                    <button
                                        wire:click="deleteAccount('{{ $account->id }}')"
                                        onclick="return confirm('Delete this account?')"
                                        class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                No accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $accounts->links() }}
        </div>
    </article>
</section>
