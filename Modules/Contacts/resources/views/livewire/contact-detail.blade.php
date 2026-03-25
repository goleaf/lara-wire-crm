<section class="space-y-6">
    @if ($contact->do_not_contact)
        <div class="rounded-2xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-300">
            Do Not Contact is enabled for this contact.
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $contact->full_name }}</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $contact->job_title ?? 'No title' }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    @if ($contact->account)
                        <a href="{{ route('accounts.show', $contact->account->id) }}" wire:navigate>
                            <x-contacts::account-badge :account="$contact->account" />
                        </a>
                    @endif
                    <span>Owner: {{ $contact->owner?->full_name ?? '—' }}</span>
                </div>
            </div>

            <div class="flex gap-2">
                @if (Route::has('activities.create'))
                    <a href="{{ route('activities.create', ['related_type' => $contact::class, 'related_id' => $contact->id]) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                        Log Activity
                    </a>
                @endif
                @if (Route::has('deals.create'))
                    <a href="{{ route('deals.create', ['contact_id' => $contact->id]) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                        Add to Deal
                    </a>
                @endif
                <a href="{{ route('contacts.edit', $contact->id) }}" wire:navigate class="rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white">
                    Edit
                </a>
            </div>
        </div>
    </article>

    <article class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-sm dark:border-white/10 dark:bg-slate-950/70">
        <div class="flex flex-wrap gap-2">
            @foreach ($tabs as $tabName)
                <button
                    wire:click="setTab('{{ $tabName }}')"
                    class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $tab === $tabName ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}"
                >
                    {{ $tabName }}
                </button>
            @endforeach
        </div>
    </article>

    @if ($tab === 'overview')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Mobile</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->mobile ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Lead Source</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->lead_source }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Department</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->department ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Preferred Channel</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->preferred_channel }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Birthday</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $contact->birthday?->format('M d, Y') ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Notes</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $contact->notes ?? '—' }}</dd>
                </div>
            </dl>
        </article>
    @endif

    @if ($tab === 'deals')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Deals</h3>
            <div class="mt-4 space-y-3">
                @forelse ($deals as $deal)
                    <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                        @if (Route::has('deals.show'))
                            <a href="{{ route('deals.show', $deal->id) }}" wire:navigate class="font-medium text-slate-900 underline dark:text-slate-100">{{ $deal->name }}</a>
                        @else
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $deal->name }}</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Amount: {{ number_format((float) ($deal->amount ?? 0), 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No linked deals yet.</p>
                @endforelse
            </div>
        </article>
    @endif

    @if ($tab === 'activities')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $contact::class, 'modelId' => (string) $contact->id], key('contact-timeline-'.$contact->id))
        </article>
    @endif

    @if ($tab === 'cases')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Cases</h3>
            <div class="mt-4 space-y-3">
                @forelse ($cases as $case)
                    <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                        @if (Route::has('cases.show'))
                            <a href="{{ route('cases.show', $case->id) }}" wire:navigate class="font-medium text-slate-900 underline dark:text-slate-100">{{ $case->number }} · {{ $case->title }}</a>
                        @else
                            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $case->number }} · {{ $case->title }}</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $case->status }} • {{ $case->priority }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No linked cases yet.</p>
                @endforelse
            </div>
        </article>
    @endif

    @if ($tab === 'files')
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            Files tab is ready for FileUploadZone integration.
        </article>
    @endif
</section>
