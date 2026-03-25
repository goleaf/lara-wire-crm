<div wire:poll.30s class="space-y-2">
    @forelse ($events as $event)
        @php
            $typeIcon = match ($event->type) {
                'Meeting' => '📅',
                'Demo' => '🖥️',
                'Follow-up' => '↩️',
                'Reminder' => '⏰',
                default => '📌',
            };
        @endphp
        <div class="rounded-2xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700 {{ $event->start_at && $event->start_at->isPast() ? 'opacity-60' : '' }}">
            <div class="flex items-center justify-between gap-2">
                <p class="truncate font-medium text-slate-900 dark:text-slate-100">
                    <span class="mr-1">{{ $typeIcon }}</span>
                    {{ $event->title }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $event->start_at?->format('D H:i') }}</p>
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                {{ $event->type }} • {{ $event->attendees->count() }} attendees
            </p>
        </div>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">No upcoming events.</p>
    @endforelse
</div>
