<section class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Lead Kanban</h3>
        <a href="{{ route('leads.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
            Table View
        </a>
    </div>

    <div class="grid gap-4 lg:grid-cols-5">
        @foreach ($statuses as $status)
            @php
                $columnLeads = $leadsByStatus->get($status, collect());
                $scoreTotal = $columnLeads->sum('score');
                $borderClass = match ($status) {
                    'New' => 'border-slate-300',
                    'Contacted' => 'border-blue-300',
                    'Qualified' => 'border-emerald-300',
                    'Unqualified' => 'border-rose-300',
                    default => 'border-purple-300',
                };
            @endphp
            <article class="rounded-3xl border-t-4 {{ $borderClass }} border border-white/70 bg-white/80 p-4 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $status }}</h4>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $columnLeads->count() }}</span>
                </div>
                <p class="mb-3 text-xs text-slate-500 dark:text-slate-400">Total score: {{ $scoreTotal }}</p>

                <div class="space-y-3">
                    @forelse ($columnLeads as $lead)
                        <article class="rounded-2xl border border-slate-200 bg-white p-3 text-sm dark:border-slate-700 dark:bg-slate-900/70">
                            <a href="{{ route('leads.show', $lead->id) }}" wire:navigate class="font-medium text-slate-900 hover:text-sky-600 dark:text-slate-100 dark:hover:text-sky-300">
                                {{ $lead->full_name }}
                            </a>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $lead->company ?? 'No company' }}</p>
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>{{ $lead->rating }}</span>
                                <span>{{ $lead->score }}/100</span>
                            </div>
                            <div class="mt-2">
                                <select wire:change="moveLead('{{ $lead->id }}', $event.target.value)" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900">
                                    @foreach ($statuses as $statusOption)
                                        <option value="{{ $statusOption }}" @selected($lead->status === $statusOption)>{{ $statusOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 p-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            No leads in this stage.
                        </p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </div>
</section>
