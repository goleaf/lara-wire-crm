<div>
    @if (! $event)
        <p class="text-sm text-slate-500 dark:text-slate-400">Event not found.</p>
    @else
        @php
            $statusClass = match ($event->status) {
                'Completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                'Cancelled' => 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                default => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            };
        @endphp

        <div class="space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $event->title }}</h5>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $event->type }}</p>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $event->status }}</span>
            </div>

            <dl class="grid gap-3 md:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Starts</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->start_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Ends</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->end_at?->format('Y-m-d H:i') ?? ($event->all_day ? 'All day' : '—') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Organizer</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->organizer?->full_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->location ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Contact</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->contact?->full_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Deal</dt>
                    <dd class="mt-1 text-sm text-slate-900 dark:text-slate-100">{{ $event->deal?->name ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Description</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $event->description ?? '—' }}</dd>
                </div>
            </dl>

            <div>
                <h6 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Attendees</h6>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($event->attendees as $attendee)
                        <span class="rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-700 dark:text-slate-300">{{ $attendee->full_name }}</span>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">No attendees.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-2">
                @if ($event->status === 'Scheduled')
                    <button wire:click="markComplete" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Mark Complete</button>
                @endif
                <button wire:click="deleteEvent" onclick="return confirm('Delete this event?')" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Delete</button>
            </div>
        </div>
    @endif
</div>
