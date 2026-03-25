<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Deal Pipeline</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('deals.list') }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Table View</a>
                <a href="{{ route('deals.create') }}" wire:navigate class="crm-btn crm-btn-primary">New Deal</a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-4">
            <select wire:model.live="pipelineFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($pipelines as $pipeline)
                    <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All Owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
            <input wire:model.live="dateFrom" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.live="dateTo" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
        </div>
    </x-crm.card>

    <div class="grid gap-4 xl:grid-cols-6">
        @foreach ($stages as $stage)
            @php
                $columnDeals = $deals->get($stage->id, collect());
                $columnTotal = $columnDeals->sum('amount');
            @endphp
            <x-crm.card class="p-4" style="border-top: 4px solid {{ $stage->color }}">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $stage->name }}</h4>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $columnDeals->count() }}</span>
                </div>
                <p class="mb-3 text-xs text-slate-500 dark:text-slate-400">Total: {{ number_format((float) $columnTotal, 2) }}</p>

                <div class="space-y-3">
                    @forelse ($columnDeals as $deal)
                        @php
                            $probClass = $deal->probability < 30
                                ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300'
                                : ($deal->probability <= 60
                                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300');
                            $isOverdue = $deal->close_date && $deal->close_date->isPast();
                        @endphp
                        <article class="rounded-2xl border border-slate-200 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900/70 {{ $deal->is_won ? 'ring-1 ring-emerald-400/60' : '' }} {{ $deal->is_lost ? 'opacity-70' : '' }}">
                            <a href="{{ route('deals.show', $deal->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                {{ $deal->name }}
                            </a>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $deal->account?->name ?? 'No account' }}</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float) $deal->amount, 2) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Expected {{ number_format($deal->expected_revenue, 2) }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $probClass }}">{{ $deal->probability }}%</span>
                                <span class="text-xs {{ $isOverdue ? 'text-rose-600 dark:text-rose-300' : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ $deal->close_date?->format('Y-m-d') ?? 'No date' }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <select wire:change="moveDeal('{{ $deal->id }}', $event.target.value)" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900">
                                    @foreach ($stages as $option)
                                        <option value="{{ $option->id }}" @selected($option->id === $stage->id)>{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 p-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No deals in this stage.
                        </p>
                    @endforelse
                </div>
            </x-crm.card>
        @endforeach
    </div>
</section>
