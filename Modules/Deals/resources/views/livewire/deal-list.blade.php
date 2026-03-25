<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Deals Table</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('deals.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Kanban</a>
                <a href="{{ route('deals.create') }}" wire:navigate class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">New Deal</a>
            </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-4">
            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
            <select wire:model.live="stageFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All stages</option>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="bulkOwnerId" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Bulk owner</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
            <button wire:click="bulkAssignOwner" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Apply Owner</button>
        </div>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <select wire:model.live="bulkStageId" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Bulk stage</option>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                @endforeach
            </select>
            <button wire:click="bulkChangeStage" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Apply Stage</button>
        </div>
    </article>

    <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100/80 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Select</th>
                        <th class="px-4 py-3"><button wire:click="sort('name')">Name</button></th>
                        <th class="px-4 py-3">Account</th>
                        <th class="px-4 py-3"><button wire:click="sort('amount')">Amount</button></th>
                        <th class="px-4 py-3"><button wire:click="sort('close_date')">Close Date</button></th>
                        <th class="px-4 py-3"><button wire:click="sort('probability')">Probability</button></th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Stage</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($deals as $deal)
                        @php
                            $statusClass = $deal->is_won
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300'
                                : ($deal->is_lost
                                    ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300'
                                    : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300');
                            $statusLabel = $deal->is_won ? 'Won' : ($deal->is_lost ? 'Lost' : 'Open');
                        @endphp
                        <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-950/30 dark:even:bg-slate-900/30">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:click="toggleSelection('{{ $deal->id }}')" @checked(in_array($deal->id, $selected, true)) />
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('deals.show', $deal->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                    {{ $deal->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $deal->account?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-900 dark:text-slate-100">
                                {{ number_format((float) $deal->amount, 2) }}
                                <p class="text-xs text-slate-500 dark:text-slate-400">Expected {{ number_format($deal->expected_revenue, 2) }}</p>
                            </td>
                            <td class="px-4 py-3 {{ $deal->close_date && $deal->close_date->isPast() ? 'text-rose-600 dark:text-rose-300' : 'text-slate-600 dark:text-slate-300' }}">
                                {{ $deal->close_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $deal->probability }}%</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <select wire:change="updateStage('{{ $deal->id }}', $event.target.value)" class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900">
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->id }}" @selected($deal->stage_id === $stage->id)>{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('deals.edit', $deal->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                No deals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
            {{ $deals->links() }}
        </div>
    </article>
</section>
