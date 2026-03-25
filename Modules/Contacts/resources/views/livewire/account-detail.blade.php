<section class="space-y-6">
    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $account->name }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <x-contacts::account-badge :account="$account" />
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ $account->industry }}</span>
                    <span>Owner: {{ $account->owner?->full_name ?? '—' }}</span>
                    <span>Created: {{ $account->created_at?->format('M d, Y') }}</span>
                </div>
            </div>

            <a href="{{ route('accounts.edit', $account->id) }}" wire:navigate class="crm-btn crm-btn-secondary">
                Edit Account
            </a>
        </div>
    </x-crm.card>

    <article class="sticky top-2 z-10 rounded-2xl border border-white/70 bg-white/90 p-3 shadow-sm backdrop-blur dark:border-white/10 dark:bg-slate-950/70">
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
        <x-crm.card class="p-6">
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Website</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->website ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Parent Account</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->parent?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Annual Revenue</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->annual_revenue ? number_format((float) $account->annual_revenue, 2) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Employees</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $account->employee_count ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Billing Address</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">
                        <x-contacts::address-block :address="$account->billing_address ?? []" />
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Shipping Address</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">
                        <x-contacts::address-block :address="$account->shipping_address ?? []" />
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Tags</dt>
                    <dd class="mt-2 flex flex-wrap gap-2">
                        @forelse ($account->tags ?? [] as $tag)
                            <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700 dark:bg-sky-500/20 dark:text-sky-300">{{ $tag }}</span>
                        @empty
                            <span class="text-sm text-slate-500 dark:text-slate-400">No tags</span>
                        @endforelse
                    </dd>
                </div>
            </dl>
        </x-crm.card>
    @endif

    @if ($tab === 'contacts')
        <x-crm.card class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Contacts</h3>
                <a href="{{ route('contacts.create', ['account_id' => $account->id]) }}" wire:navigate class="rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white">Quick Add</a>
            </div>
            <div class="space-y-3">
                @forelse ($account->contacts as $contact)
                    <a href="{{ route('contacts.show', $contact->id) }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm hover:border-sky-300 dark:border-slate-700 dark:hover:border-sky-400">
                        <div class="flex items-center gap-3">
                            <x-contacts::contact-avatar :contact="$contact" />
                            <div>
                                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $contact->full_name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $contact->job_title ?? 'No title' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $contact->owner?->full_name ?? '—' }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No contacts linked yet.</p>
                @endforelse
            </div>
        </x-crm.card>
    @endif

    @if ($tab === 'deals')
        <x-crm.card class="p-6">
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
        </x-crm.card>
    @endif

    @if ($tab === 'activities')
        <x-crm.card class="p-6">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $account::class, 'modelId' => (string) $account->id], key('account-timeline-'.$account->id))
        </x-crm.card>
    @endif

    @if ($tab === 'files')
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            Files tab is ready for FileUploadZone integration.
        </article>
    @endif

    @if ($tab === 'notes')
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            Notes section placeholder.
        </article>
    @endif
</section>
