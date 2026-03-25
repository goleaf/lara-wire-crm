<section class="space-y-6">
    <x-crm.status />

    @php
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
    @endphp

    <x-crm.card class="p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $activity->subject }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span>{{ $activity->type }}</span>
                    <span>•</span>
                    <span class="{{ $priorityClass }}">{{ $activity->priority }}</span>
                    <span>•</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $activity->status }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('activities.edit', $activity->id) }}" wire:navigate class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                    Edit
                </a>
                @if ($activity->status === 'Planned')
                    <button wire:click="markComplete" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">
                        Complete
                    </button>
                    <button wire:click="cancel" class="rounded-xl bg-slate-700 px-3 py-2 text-xs font-semibold text-white">
                        Cancel
                    </button>
                @endif
                @can('activities.delete')
                    <button wire:click="delete" onclick="return confirm('Delete this activity?')" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">
                        Delete
                    </button>
                @endcan
            </div>
        </div>
    </x-crm.card>

    <x-crm.card class="p-6">
        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Due Date</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $activity->due_date?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Duration</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $activity->duration_minutes ? $activity->duration_minutes.' min' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Owner</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $activity->owner?->full_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Related Record</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">
                    @if ($relatedLink['url'])
                        <a href="{{ $relatedLink['url'] }}" wire:navigate class="underline">{{ $relatedLink['label'] }}</a>
                    @else
                        {{ $relatedLink['label'] }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Reminder</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $activity->reminder_at?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-slate-500">Completed At</dt>
                <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $activity->completed_at?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Description</dt>
                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $activity->description ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Outcome</dt>
                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $activity->outcome ?? '—' }}</dd>
            </div>
        </dl>
    </x-crm.card>

    <x-crm.card class="p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Attendees</h3>
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @forelse ($activity->attendees as $attendee)
                <div class="rounded-2xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                    {{ $attendee->full_name }}
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No attendees assigned.</p>
            @endforelse
        </div>
    </x-crm.card>
</section>
