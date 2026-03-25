<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @php
        $priorityClass = match ($supportCase->priority) {
            'Critical' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200',
            'High' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200',
            'Medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
        };
        $statusClass = match ($supportCase->status) {
            'Open' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
            'In Progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-200',
            'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200',
            'Resolved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200',
            default => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
        $typeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200';

        $slaTextClass = 'text-slate-600 dark:text-slate-300';
        $slaLabel = 'No SLA deadline';
        if ($supportCase->is_overdue) {
            $slaTextClass = 'text-rose-700 dark:text-rose-300';
            $slaLabel = 'BREACHED';
        } elseif ($supportCase->sla_deadline) {
            $totalMinutes = max((int) $supportCase->created_at?->diffInMinutes($supportCase->sla_deadline), 1);
            $remainingMinutes = (int) now()->diffInMinutes($supportCase->sla_deadline, false);
            $remainingPercent = ($remainingMinutes / $totalMinutes) * 100;
            $slaTextClass = $remainingPercent > 50
                ? 'text-emerald-700 dark:text-emerald-300'
                : ($remainingPercent > 20 ? 'text-amber-700 dark:text-amber-300' : 'text-rose-700 dark:text-rose-300');
            $slaLabel = $supportCase->sla_deadline->format('Y-m-d H:i').' ('.$supportCase->sla_deadline->diffForHumans().')';
        }
    @endphp

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $supportCase->number }}</h2>
                <p class="mt-1 text-lg text-slate-800 dark:text-slate-100">{{ $supportCase->title }}</p>
                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full px-2 py-0.5 {{ $priorityClass }}">{{ $supportCase->priority }}</span>
                    <span class="rounded-full px-2 py-0.5 {{ $statusClass }}">{{ $supportCase->status }}</span>
                    <span class="rounded-full px-2 py-0.5 {{ $typeClass }}">{{ $supportCase->type }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cases.edit', $supportCase->id) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Edit</a>
                <a href="{{ route('cases.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Back</a>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white/90 p-4 dark:border-slate-700 dark:bg-slate-950/60">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">SLA Countdown</p>
            <p class="mt-2 text-sm font-semibold {{ $slaTextClass }}">{{ $slaLabel }}</p>
        </div>
    </article>

    <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="grid gap-4 md:grid-cols-5">
            @foreach ($statusFlow as $statusStep)
                <button
                    type="button"
                    wire:click="changeStatus('{{ $statusStep }}')"
                    class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $supportCase->status === $statusStep ? 'bg-sky-600 text-white' : 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}"
                >
                    {{ $statusStep }}
                </button>
            @endforeach
        </div>
    </article>

    <div class="grid gap-4 md:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Account</p>
            <p class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $supportCase->account?->name ?? '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Contact</p>
            <p class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">
                @if ($supportCase->contact)
                    <a href="{{ route('contacts.show', $supportCase->contact->id) }}" wire:navigate class="underline">{{ $supportCase->contact->full_name }}</a>
                @else
                    —
                @endif
            </p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Owner</p>
            <p class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $supportCase->owner?->full_name ?? '—' }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Channel</p>
            <p class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $supportCase->channel }}</p>
        </article>
    </div>

    <article class="rounded-2xl border border-white/70 bg-white/90 p-3 shadow-sm dark:border-white/10 dark:bg-slate-950/70">
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="setTab('comments')" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $tab === 'comments' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">Comments</button>
            <button type="button" wire:click="setTab('activities')" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $tab === 'activities' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">Activities</button>
            <button type="button" wire:click="setTab('files')" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $tab === 'files' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">Files</button>
            <button type="button" wire:click="setTab('linked')" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wide {{ $tab === 'linked' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">Linked Records</button>
        </div>
    </article>

    @if ($tab === 'comments')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Comments</h3>
            <div class="mt-4 space-y-3">
                @forelse ($supportCase->comments as $comment)
                    <div class="rounded-2xl border px-4 py-3 {{ $comment->is_internal ? 'border-amber-300 bg-amber-50/70 dark:border-amber-500/30 dark:bg-amber-500/10' : 'border-slate-200 bg-white/90 dark:border-slate-700 dark:bg-slate-950/60' }}">
                        <div class="flex items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $comment->user?->full_name ?? 'Unknown' }}</span>
                            <span>{{ $comment->created_at?->format('Y-m-d H:i') }}</span>
                        </div>
                        @if ($comment->is_internal)
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700 dark:text-amber-300">Internal Note</p>
                        @endif
                        <p class="mt-2 text-sm text-slate-800 dark:text-slate-200">{{ $comment->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No comments yet.</p>
                @endforelse
            </div>

            <form wire:submit="addComment" class="mt-5 space-y-3">
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Add Comment</span>
                    <textarea wire:model.live.debounce.300ms="commentBody" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                    @error('commentBody')
                        <span class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</span>
                    @enderror
                </label>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <input type="checkbox" wire:model.live="commentInternal">
                    Internal Note
                </label>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-500">Submit Comment</button>
                </div>
            </form>
        </article>
    @elseif ($tab === 'activities')
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            @livewire(\Modules\Core\Livewire\ActivityTimeline::class, ['modelType' => $supportCase::class, 'modelId' => (string) $supportCase->id], key('case-timeline-'.$supportCase->id))
        </article>
    @elseif ($tab === 'files')
        <article class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-400">
            Files tab placeholder. Integrate with Files module picker/list.
        </article>
    @else
        <article class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Linked Records</h3>
            <dl class="mt-4 grid gap-4 md:grid-cols-3">
                <div>
                    <dt class="text-xs text-slate-500 dark:text-slate-400">Account</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $supportCase->account?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 dark:text-slate-400">Contact</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $supportCase->contact?->full_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 dark:text-slate-400">Deal</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $supportCase->deal?->name ?? '—' }}</dd>
                </div>
            </dl>
        </article>
    @endif

    @if (in_array($supportCase->status, ['Resolved', 'Closed'], true))
        @livewire(\Modules\Cases\Livewire\CaseSatisfactionForm::class, ['id' => $supportCase->id], key('csat-'.$supportCase->id))
    @endif
</section>
