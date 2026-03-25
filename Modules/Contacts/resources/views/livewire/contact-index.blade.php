<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Contacts</h3>
            <a
                href="{{ route('contacts.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500"
            >
                New Contact
            </a>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-5">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search name, email, phone"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
            />

            <select wire:model.live="accountFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Accounts</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="doNotContactFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Contact preference</option>
                <option value="1">Do Not Contact</option>
                <option value="0">Can Contact</option>
            </select>

            <select wire:model.live="leadSourceFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Sources</option>
                @foreach ($leadSources as $source)
                    <option value="{{ $source }}">{{ $source }}</option>
                @endforeach
            </select>
        </div>
    </article>

    <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Full Name</th>
                        <th class="px-4 py-3">Account</th>
                        <th class="px-4 py-3">Job Title</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Channel</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($contacts as $contact)
                        <tr class="{{ $contact->do_not_contact ? 'bg-rose-50/80 dark:bg-rose-950/20' : 'odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30' }}">
                            <td class="px-4 py-3">
                                <a href="{{ route('contacts.show', $contact->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                    {{ $contact->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                @if ($contact->account)
                                    <x-contacts::account-badge :account="$contact->account" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->job_title ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->owner?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->preferred_channel }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('contacts.edit', $contact->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        Edit
                                    </a>
                                    <button
                                        wire:click="deleteContact('{{ $contact->id }}')"
                                        onclick="return confirm('Delete this contact?')"
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
                                No contacts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $contacts->links() }}
        </div>
    </article>
</section>
