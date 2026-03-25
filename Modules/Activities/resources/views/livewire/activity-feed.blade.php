<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Activity Feed</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('activities.mine') }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    My Activities
                </a>
                <a href="{{ route('activities.create') }}" wire:navigate class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                    New Activity
                </a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-5">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search subject or description" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />

            <select wire:model.live="typeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>

            <select wire:model.live="ownerFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">All owners</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="relatedFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                <option value="">Any related type</option>
                @foreach ($relatedOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </article>

    @forelse ($groupedActivities as $group => $groupItems)
        <article class="space-y-3">
            <h4 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">{{ $group }}</h4>
            @foreach ($groupItems as $activity)
                @php
                    $isExpanded = in_array($activity->id, $expanded, true);
                    $statusClass = match ($activity->status) {
                        'Completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                        'Cancelled' => 'bg-slate-200 text-slate-600 line-through dark:bg-slate-700 dark:text-slate-300',
                        default => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                    };
                    $priorityClass = match ($activity->priority) {
                        'High' => 'text-rose-600 dark:text-rose-300',
                        'Low' => 'text-slate-500 dark:text-slate-400',
                        default => 'text-blue-600 dark:text-blue-300',
                    };
                    $typeIcon = match ($activity->type) {
                        'Meeting' => '📅',
                        'Task' => '✅',
                        'Note' => '📝',
                        default => '💬',
                    };
                    $relatedLabel = class_basename((string) $activity->related_to_type);
                @endphp
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm dark:border-white/10 dark:bg-slate-950/40 {{ $activity->status === 'Planned' && $activity->due_date && $activity->due_date->isPast() ? 'border-l-4 border-l-rose-500' : '' }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">{{ $typeIcon }}</span>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $activity->due_date?->format('Y-m-d H:i') ?? 'No due date' }}
                                    @if ($activity->related_to_type && $activity->related_to_id)
                                        • {{ $relatedLabel }} #{{ str($activity->related_to_id)->substr(0, 8) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $activity->status }}</span>
                            <span class="text-xs font-semibold {{ $priorityClass }}">{{ $activity->priority }}</span>
                            <button wire:click="toggleExpand('{{ $activity->id }}')" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                                {{ $isExpanded ? 'Hide' : 'Expand' }}
                            </button>
                            @if ($activity->status === 'Planned')
                                <button wire:click="markComplete('{{ $activity->id }}')" class="rounded-lg border border-emerald-300 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/40 dark:text-emerald-300">
                                    Mark Complete
                                </button>
                            @endif
                            <a href="{{ route('activities.show', $activity->id) }}" wire:navigate class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                                View
                            </a>
                        </div>
                    </div>

                    @if ($isExpanded)
                        <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-sm dark:border-slate-800 dark:bg-slate-900/40">
                            <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-300">{{ $activity->description ?: 'No description provided.' }}</p>
                            @if ($activity->outcome)
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Outcome: {{ $activity->outcome }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Owner: {{ $activity->owner?->full_name ?? '—' }}
                                • Attendees: {{ $activity->attendees->pluck('full_name')->implode(', ') ?: 'None' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </article>
    @empty
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            No activities found.
        </article>
    @endforelse

    <article class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        {{ $activities->links() }}
    </article>
</section>
