<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $lead->full_name }}</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $lead->company ?? 'No company' }}</p>
                @php
                    $statusClass = match ($lead->status) {
                        'New' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
                        'Contacted' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                        'Qualified' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                        'Unqualified' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                        default => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                    };
                    $ratingClass = match ($lead->rating) {
                        'Hot' => 'text-rose-600 dark:text-rose-300',
                        'Warm' => 'text-amber-600 dark:text-amber-300',
                        default => 'text-blue-600 dark:text-blue-300',
                    };
                    $scoreClass = $lead->score <= 30 ? 'bg-rose-500' : ($lead->score <= 60 ? 'bg-amber-500' : 'bg-emerald-500');
                @endphp
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $statusClass }}">{{ $lead->status }}</span>
                    <span class="font-semibold {{ $ratingClass }}">{{ $lead->rating }}</span>
                    <span class="text-slate-500 dark:text-slate-400">Owner: {{ $lead->owner?->full_name ?? '—' }}</span>
                    @if ($lead->campaign)
                        <span class="text-slate-500 dark:text-slate-400">Campaign: {{ $lead->campaign->name }}</span>
                    @endif
                </div>
            </div>

            <div class="w-44">
                <div class="mb-2 text-xs uppercase tracking-wide text-slate-500">Score</div>
                <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-800">
                    <div class="h-3 rounded-full {{ $scoreClass }}" style="width: {{ $lead->score }}%"></div>
                </div>
                <div class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $lead->score }}/100</div>
            </div>
        </div>

        @if ($lead->converted)
            <div class="mt-5 rounded-2xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                Converted at {{ $lead->converted_at?->format('M d, Y H:i') }}.
                @if ($lead->convertedContact)
                    <a href="{{ route('contacts.show', $lead->convertedContact->id) }}" wire:navigate class="ml-1 underline">Contact</a>
                @endif
                @if ($lead->convertedDeal && Route::has('deals.show'))
                    <a href="{{ route('deals.show', $lead->convertedDeal->id) }}" wire:navigate class="ml-2 underline">Deal</a>
                @endif
            </div>
        @endif
    </x-crm.card>

    <article class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-sm dark:border-white/10 dark:bg-slate-950/70">
        <div class="flex flex-wrap items-center justify-between gap-2">
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
            <div class="flex gap-2">
                <a href="{{ route('leads.edit', $lead->id) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    Edit
                </a>
                @if (! $lead->converted)
                    <button wire:click="openConvertModal" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">
                        Convert Lead
                    </button>
                @endif
            </div>
        </div>
    </article>

    @if ($tab === 'overview')
        <x-crm.card class="p-6">
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $lead->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $lead->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Lead Source</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $lead->lead_source }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Created</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $lead->created_at?->format('M d, Y H:i') }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Description</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $lead->description ?? '—' }}</dd>
                </div>
            </dl>
        </x-crm.card>
    @endif

    @if ($tab === 'activities')
        <x-crm.card class="p-6">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $lead::class, 'modelId' => (string) $lead->id], key('lead-timeline-'.$lead->id))
        </x-crm.card>
    @elseif ($tab !== 'overview')
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            {{ ucfirst($tab) }} tab placeholder.
        </article>
    @endif

    @if ($showConvertModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
            <div class="w-full max-w-3xl rounded-3xl border border-white/20 bg-white p-4 shadow-xl dark:bg-slate-950">
                <livewire:is :component="\Modules\Leads\Livewire\LeadConvertModal::class" :lead-id="$lead->id" :key="'lead-convert-'.$lead->id" />
                <div class="mt-3 text-right">
                    <button wire:click="closeConvertModal" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</section>
