<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $deal->name }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <a href="{{ route('accounts.show', $deal->account_id) }}" wire:navigate class="underline">{{ $deal->account?->name ?? 'No account' }}</a>
                    <span>•</span>
                    <span>{{ number_format((float) $deal->amount, 2) }} {{ $deal->currency }}</span>
                    <span>•</span>
                    <span>{{ $deal->stage?->name }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('deals.edit', $deal->id) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Edit</a>
                <button wire:click="markWon" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Mark Won</button>
                <button wire:click="openLostModal" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Mark Lost</button>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach ($pipelineStages as $stage)
                <button
                    wire:click="moveToStage('{{ $stage->id }}')"
                    class="rounded-full border px-3 py-1 text-xs font-semibold {{ $deal->stage_id === $stage->id ? 'border-sky-500 bg-sky-600 text-white' : 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}"
                    style="{{ $deal->stage_id === $stage->id ? '' : 'border-color: '.$stage->color }}"
                >
                    {{ $stage->name }}
                </button>
            @endforeach
        </div>
    </x-crm.card>

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
        <x-crm.card class="p-6">
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Owner</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $deal->owner?->full_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Contact</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">
                        @if ($deal->contact)
                            <a href="{{ route('contacts.show', $deal->contact->id) }}" wire:navigate class="underline">{{ $deal->contact->full_name }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Close Date</dt>
                    <dd class="mt-1 text-sm {{ $deal->close_date && $deal->close_date->isPast() ? 'text-rose-600 dark:text-rose-300' : 'text-slate-900 dark:text-slate-100' }}">{{ $deal->close_date?->format('Y-m-d') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Probability</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $deal->probability }}%</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Expected Revenue</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ number_format($deal->expected_revenue, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Source</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $deal->source ?? '—' }}</dd>
                </div>
            </dl>
        </x-crm.card>
    @endif

    @if ($tab === 'products')
        <x-crm.card class="p-6">
            <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-white">Line Items</h3>
            <div class="space-y-2">
                @forelse ($deal->products as $product)
                    <div class="grid grid-cols-5 items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700">
                        <span class="col-span-2">{{ $product->name }}</span>
                        <span>Qty {{ $product->pivot->quantity }}</span>
                        <span>Price {{ number_format((float) $product->pivot->unit_price, 2) }}</span>
                        <span class="text-right font-semibold">{{ number_format((float) $product->pivot->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No products attached.</p>
                @endforelse
            </div>
        </x-crm.card>
    @endif

    @if ($tab === 'activities')
        <x-crm.card class="p-6">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $deal::class, 'modelId' => (string) $deal->id], key('deal-timeline-'.$deal->id))
        </x-crm.card>
    @endif

    @if (in_array($tab, ['quotes', 'invoices', 'files'], true))
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            {{ ucfirst($tab) }} tab placeholder.
        </article>
    @endif

    @if ($showLostModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
            <div class="w-full max-w-md rounded-3xl border border-white/20 bg-white p-5 shadow-xl dark:bg-slate-950">
                <h4 class="text-base font-semibold text-slate-900 dark:text-white">Mark Deal as Lost</h4>
                <div class="mt-4 space-y-3">
                    <select wire:model.live="lostReason" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="Price">Price</option>
                        <option value="Competitor">Competitor</option>
                        <option value="No Budget">No Budget</option>
                        <option value="No Decision">No Decision</option>
                        <option value="Other">Other</option>
                    </select>
                    <textarea wire:model.live="lostNotes" rows="4" placeholder="Notes" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button wire:click="closeLostModal" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Cancel</button>
                    <button wire:click="markLost" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Confirm</button>
                </div>
            </div>
        </div>
    @endif
</section>
