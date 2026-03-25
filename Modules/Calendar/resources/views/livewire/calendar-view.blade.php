<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button wire:click="previousPeriod" class="rounded-xl border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Previous</button>
                <button wire:click="goToday" class="rounded-xl border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Today</button>
                <button wire:click="nextPeriod" class="rounded-xl border border-slate-300 px-3 py-2 text-sm dark:border-slate-700">Next</button>
                <h3 class="ml-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $anchor->format('F Y') }}</h3>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="setViewMode('month')" class="rounded-xl px-3 py-2 text-sm {{ $view === 'month' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Month</button>
                <button wire:click="setViewMode('week')" class="rounded-xl px-3 py-2 text-sm {{ $view === 'week' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Week</button>
                <button wire:click="setViewMode('day')" class="rounded-xl px-3 py-2 text-sm {{ $view === 'day' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Day</button>
                <button wire:click="openNewEvent('{{ $selectedDay }}')" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">
                    New Event
                </button>
            </div>
        </div>
    </article>

    @if ($view === 'month')
        <article class="grid grid-cols-7 overflow-hidden rounded-3xl border border-white/70 bg-white/90 text-sm shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                <div class="border-b border-slate-200 bg-slate-100/80 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                    {{ $dayName }}
                </div>
            @endforeach

            @foreach ($monthDays as $day)
                @php
                    $dateKey = $day->toDateString();
                    $dayEvents = $eventsByDate->get($dateKey, collect());
                    $isCurrentMonth = $day->month === $anchor->month;
                    $isToday = $day->isToday();
                @endphp
                <button wire:click="selectDay('{{ $dateKey }}')" class="group min-h-32 border border-slate-200 px-3 py-2 text-left align-top transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-900/40 {{ $isCurrentMonth ? '' : 'bg-slate-50/70 text-slate-400 dark:bg-slate-900/20 dark:text-slate-500' }} {{ $isToday ? 'bg-sky-50 dark:bg-sky-500/10' : '' }}">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-semibold {{ $isToday ? 'rounded-full bg-sky-600 px-2 py-0.5 text-white' : '' }}">{{ $day->format('j') }}</span>
                        @if ($dayEvents->count() > 0)
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $dayEvents->count() }}</span>
                        @endif
                    </div>
                    <div class="space-y-1">
                        @foreach ($dayEvents->take(3) as $event)
                            <button type="button" wire:click.stop="openEvent('{{ $event->id }}')" class="block w-full truncate rounded-md px-2 py-1 text-left text-[11px] font-medium text-white" style="background-color: {{ $event->color ?: '#0ea5e9' }}">
                                {{ $event->title }}
                            </button>
                        @endforeach
                        @if ($dayEvents->count() > 3)
                            <p class="text-[11px] italic text-slate-500 dark:text-slate-400">+{{ $dayEvents->count() - 3 }} more</p>
                        @endif
                    </div>
                </button>
            @endforeach
        </article>
    @endif

    @if ($view === 'week')
        <article class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid grid-cols-8 border-b border-slate-200 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <div class="bg-slate-100/80 px-3 py-2 dark:bg-slate-900">Time</div>
                @foreach ($weekDays as $day)
                    <div class="border-l border-slate-200 bg-slate-100/80 px-3 py-2 dark:border-slate-800 dark:bg-slate-900">
                        {{ $day->format('D j') }}
                    </div>
                @endforeach
            </div>

            @for ($hour = 0; $hour < 24; $hour++)
                <div class="grid grid-cols-8 text-xs">
                    <div class="border-b border-slate-200 px-3 py-2 text-slate-500 dark:border-slate-800 dark:text-slate-400">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}:00</div>
                    @foreach ($weekDays as $day)
                        @php
                            $items = $eventsByDate
                                ->get($day->toDateString(), collect())
                                ->filter(fn ($item) => $item->start_at && $item->start_at->hour === $hour);
                        @endphp
                        <div class="min-h-14 border-b border-l border-slate-200 px-2 py-1 dark:border-slate-800">
                            @foreach ($items as $event)
                                <button wire:click="openEvent('{{ $event->id }}')" class="mb-1 w-full rounded-md border-l-4 px-2 py-1 text-left text-[11px] text-slate-800 shadow-sm dark:text-slate-100" style="border-left-color: {{ $event->color ?: '#0ea5e9' }}; background-color: color-mix(in srgb, {{ $event->color ?: '#0ea5e9' }} 14%, white);">
                                    <p class="truncate font-semibold">{{ $event->title }}</p>
                                    <p class="text-[10px] opacity-80">{{ $event->start_at?->format('H:i') }}</p>
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endfor
        </article>
    @endif

    @if ($view === 'day')
        @php
            $dayEvents = $eventsByDate->get(\Illuminate\Support\Carbon::parse($selectedDay)->toDateString(), collect());
        @endphp
        <article class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($selectedDay)->format('l, F j, Y') }}</h4>
                <button wire:click="openNewEvent('{{ $selectedDay }}')" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">New on this day</button>
            </div>

            <div class="space-y-3">
                @forelse ($dayEvents as $event)
                    <button wire:click="openEvent('{{ $event->id }}')" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-left shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900/40 {{ $event->start_at && $event->start_at->isPast() ? 'opacity-70' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $event->title }}</p>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $event->all_day ? 'All day' : $event->start_at?->format('H:i') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $event->type }} • {{ $event->status }}</p>
                    </button>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        No events for this day.
                    </div>
                @endforelse
            </div>
        </article>
    @endif

    <article class="rounded-3xl border border-white/70 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Upcoming 7 Days</h4>
        @livewire(\Modules\Calendar\Livewire\UpcomingEvents::class)
    </article>

    @if ($showEventForm)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-4 md:items-center">
            <div class="w-full max-w-3xl rounded-3xl border border-white/20 bg-white p-5 shadow-xl dark:bg-slate-950">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Event Form</h4>
                    <button wire:click="closeOverlays" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">Close</button>
                </div>
                @livewire(
                    \Modules\Calendar\Livewire\EventForm::class,
                    ['eventId' => $editingEventId, 'selectedDate' => $eventFormDate],
                    key('event-form-'.($editingEventId ?? 'new').'-'.$eventFormDate)
                )
            </div>
        </div>
    @endif

    @if ($showEventDetail && $selectedEventId)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/50 p-4 md:items-center">
            <div class="w-full max-w-2xl rounded-3xl border border-white/20 bg-white p-5 shadow-xl dark:bg-slate-950">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Event Detail</h4>
                    <button wire:click="closeOverlays" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-700">Close</button>
                </div>
                @livewire(
                    \Modules\Calendar\Livewire\EventDetail::class,
                    ['eventId' => $selectedEventId],
                    key('event-detail-'.$selectedEventId)
                )
            </div>
        </div>
    @endif
</section>
