<div class="space-y-4">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-slate-700 dark:bg-slate-950/40">
        <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Add Activity</h4>
        <div class="mt-3 grid gap-3 md:grid-cols-5">
            <select wire:model.live="newType" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            <input wire:model.live.debounce.300ms="newSubject" type="text" placeholder="Subject" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900 md:col-span-2" />
            <input wire:model.live="newDueDate" type="datetime-local" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <select wire:model.live="newOwnerId" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-3 flex justify-end">
            <button wire:click="addActivity" class="rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white">Add</button>
        </div>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-slate-700 dark:bg-slate-950/40">
        <div class="mb-3 flex flex-wrap gap-2">
            <button wire:click="$set('typeFilter', '')" class="rounded-full px-3 py-1 text-xs {{ $typeFilter === '' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">All</button>
            @foreach ($types as $type)
                <button wire:click="$set('typeFilter', '{{ $type }}')" class="rounded-full px-3 py-1 text-xs {{ $typeFilter === $type ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">{{ $type }}</button>
            @endforeach
        </div>

        <div class="space-y-3">
            @forelse ($activities as $activity)
                @php
                    $leftBorder = $activity->status === 'Planned' && $activity->due_date && $activity->due_date->isPast()
                        ? 'border-l-rose-500'
                        : ($activity->due_date && $activity->due_date->isToday() ? 'border-l-amber-500' : 'border-l-sky-500');
                @endphp
                <div class="rounded-2xl border border-slate-200 border-l-4 {{ $leftBorder }} bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950/40">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $activity->subject }}</p>
                        <div class="flex items-center gap-2">
                            @if ($activity->status === 'Planned')
                                <button wire:click="markComplete('{{ $activity->id }}')" class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 dark:border-emerald-500/40 dark:text-emerald-300">Done</button>
                            @endif
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->status }}</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $activity->type }} • {{ $activity->owner?->full_name ?? '—' }} • {{ $activity->due_date?->format('Y-m-d H:i') ?? 'No due date' }}</p>
                    @if ($activity->description)
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">{{ $activity->description }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No activities found for this record.</p>
            @endforelse
        </div>
    </article>
</div>
