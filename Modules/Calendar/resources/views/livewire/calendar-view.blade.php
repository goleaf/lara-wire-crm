<section class="space-y-6">
    <x-crm.status />

    <x-crm.card class="p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button wire:click="previousPeriod" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" wire:loading.attr="disabled" class="rounded-xl border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700">Previous</button>
                <button wire:click="goToday" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" wire:loading.attr="disabled" class="rounded-xl border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700">Today</button>
                <button wire:click="nextPeriod" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" wire:loading.attr="disabled" class="rounded-xl border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700">Next</button>
                <h3 class="ml-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $anchor->format('F Y') }}</h3>
                <span wire:loading wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" class="text-xs text-slate-500 dark:text-slate-400">Updating...</span>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="setViewMode('month')" wire:target="setViewMode" wire:loading.attr="disabled" class="rounded-xl px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 {{ $view === 'month' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Month</button>
                <button wire:click="setViewMode('week')" wire:target="setViewMode" wire:loading.attr="disabled" class="rounded-xl px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 {{ $view === 'week' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Week</button>
                <button wire:click="setViewMode('day')" wire:target="setViewMode" wire:loading.attr="disabled" class="rounded-xl px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 {{ $view === 'day' ? 'bg-sky-600 text-white' : 'border border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300' }}">Day</button>
                <button wire:click="openNewEvent('{{ $selectedDay }}')" wire:loading.attr="disabled" class="crm-btn crm-btn-primary disabled:cursor-not-allowed disabled:opacity-60">
                    New Event
                </button>
            </div>
        </div>
    </x-crm.card>

    <x-crm.card class="p-5 shadow-sm">
        @php
            $agendaItems = collect($rangeEvents);
        @endphp

        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Range Agenda</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ \Illuminate\Support\Carbon::parse($rangeFrom)->format('M j, Y') }} - {{ \Illuminate\Support\Carbon::parse($rangeTo)->format('M j, Y') }}
                </p>
            </div>
            <span class="rounded-full border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:text-slate-300">{{ $agendaItems->count() }} events</span>
        </div>

        <div class="space-y-2">
            @forelse ($agendaItems->take(8) as $event)
                @php
                    $startsAt = ($event['start_at'] ?? null) ? \Illuminate\Support\Carbon::parse($event['start_at']) : null;
                @endphp
                <button
                    type="button"
                    wire:key="range-event-{{ $event['id'] }}"
                    wire:click="openEvent('{{ $event['id'] }}')"
                    class="block w-full rounded-xl border-l-4 border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/70 dark:hover:bg-slate-900"
                    style="border-left-color: {{ $event['color'] ?: '#0ea5e9' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $event['title'] }}</p>
                        <span class="shrink-0 text-[11px] text-slate-500 dark:text-slate-400">{{ $event['all_day'] ? 'All day' : ($startsAt?->format('D H:i') ?? 'No time') }}</span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{{ $event['type'] }} • {{ $event['status'] }} • {{ $event['organizer'] ?: 'Unassigned' }}</p>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    No events in the selected range.
                </div>
            @endforelse
        </div>

        @if ($agendaItems->count() > 8)
            <p class="mt-2 text-xs italic text-slate-500 dark:text-slate-400">+{{ $agendaItems->count() - 8 }} more in this range</p>
        @endif
    </x-crm.card>

    @if ($view === 'month')
        <article wire:loading.class="opacity-60" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 text-sm shadow-sm transition-opacity dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid grid-cols-7 border-b border-slate-200 dark:border-slate-800">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="bg-slate-100/80 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7">
                @foreach ($monthDays as $day)
                    @php
                        $dateKey = $day->toDateString();
                        $dayEvents = $eventsByDate->get($dateKey, collect());
                        $isCurrentMonth = $day->month === $anchor->month;
                        $isToday = $day->isToday();
                    @endphp
                    <div
                        wire:key="month-day-{{ $dateKey }}"
                        @class([
                            'min-h-40 border-b border-r border-slate-200 px-3 py-2 align-top dark:border-slate-800',
                            'bg-slate-50/70 text-slate-400 dark:bg-slate-900/20 dark:text-slate-500' => ! $isCurrentMonth,
                            'bg-sky-50/70 dark:bg-sky-500/10' => $isToday,
                        ])
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <button
                                type="button"
                                wire:click="selectDay('{{ $dateKey }}')"
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold transition hover:bg-slate-200/70 dark:hover:bg-slate-800"
                            >
                                <span class="{{ $isToday ? 'rounded-full bg-sky-600 px-2 py-0.5 text-white' : '' }}">{{ $day->format('j') }}</span>
                            </button>

                            @if ($dayEvents->count() > 0)
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $dayEvents->count() }}</span>
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($dayEvents->take(4) as $event)
                                <button
                                    type="button"
                                    wire:key="month-event-{{ $event->id }}-{{ $dateKey }}"
                                    wire:click="openEvent('{{ $event->id }}')"
                                    class="block w-full rounded-md border-l-4 bg-white/90 px-2 py-1.5 text-left shadow-sm transition hover:bg-white dark:bg-slate-900/70 dark:hover:bg-slate-900"
                                    style="border-left-color: {{ $event->color ?: '#0ea5e9' }}"
                                >
                                    <p class="truncate text-[11px] font-semibold text-slate-900 dark:text-slate-100">{{ $event->title }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-400">{{ $event->all_day ? 'All day' : $event->start_at?->format('H:i') }}</p>
                                </button>
                            @endforeach

                            @if ($dayEvents->count() > 4)
                                <button
                                    type="button"
                                    wire:click="selectDay('{{ $dateKey }}')"
                                    class="text-[11px] italic text-slate-500 transition hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                                >
                                    +{{ $dayEvents->count() - 4 }} more
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    @endif

    @if ($view === 'week')
        <article wire:loading.class="opacity-60" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-sm transition-opacity dark:border-white/10 dark:bg-slate-950/40">
            <div class="grid grid-cols-8 border-b border-slate-200 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <div class="bg-slate-100/80 px-3 py-2 dark:bg-slate-900">Time</div>
                @foreach ($weekDays as $day)
                    <div wire:key="week-header-{{ $day->toDateString() }}" class="border-l border-slate-200 bg-slate-100/80 px-3 py-2 dark:border-slate-800 dark:bg-slate-900">
                        {{ $day->format('D j') }}
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-8 border-b border-slate-200 text-xs dark:border-slate-800">
                <div class="bg-slate-100/80 px-3 py-2 font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">All day</div>
                @foreach ($weekDays as $day)
                    @php
                        $allDayItems = $eventsByDate
                            ->get($day->toDateString(), collect())
                            ->filter(fn ($item) => (bool) $item->all_day);
                    @endphp
                    <div wire:key="week-all-day-{{ $day->toDateString() }}" class="min-h-16 border-l border-slate-200 px-2 py-2 dark:border-slate-800">
                        @foreach ($allDayItems->take(2) as $event)
                            <button
                                type="button"
                                wire:key="week-allday-{{ $event->id }}-{{ $day->toDateString() }}"
                                wire:click="openEvent('{{ $event->id }}')"
                                class="mb-1 block w-full rounded-md border-l-4 bg-white/90 px-2 py-1 text-left text-[11px] font-semibold text-slate-900 shadow-sm dark:bg-slate-900/70 dark:text-slate-100"
                                style="border-left-color: {{ $event->color ?: '#0ea5e9' }}"
                            >
                                <span class="truncate">{{ $event->title }}</span>
                            </button>
                        @endforeach

                        @if ($allDayItems->count() > 2)
                            <button
                                type="button"
                                wire:click="selectDay('{{ $day->toDateString() }}')"
                                class="text-[10px] italic text-slate-500 dark:text-slate-400"
                            >
                                +{{ $allDayItems->count() - 2 }} more
                            </button>
                        @endif
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
                                ->filter(fn ($item) => ! $item->all_day && $item->start_at && $item->start_at->hour === $hour);
                        @endphp
                        <div wire:key="week-hour-{{ $day->toDateString() }}-{{ $hour }}" class="min-h-14 border-b border-l border-slate-200 px-2 py-1 dark:border-slate-800">
                            @foreach ($items as $event)
                                <button
                                    type="button"
                                    wire:key="week-event-{{ $event->id }}-{{ $day->toDateString() }}-{{ $hour }}"
                                    wire:click="openEvent('{{ $event->id }}')"
                                    class="mb-1 w-full rounded-md border-l-4 bg-white/90 px-2 py-1 text-left text-[11px] text-slate-900 shadow-sm transition hover:bg-white dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900"
                                    style="border-left-color: {{ $event->color ?: '#0ea5e9' }}"
                                >
                                    <p class="truncate font-semibold">{{ $event->title }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $event->start_at?->format('H:i') }}</p>
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
        <article wire:loading.class="opacity-60" wire:target="previousPeriod,goToday,nextPeriod,setViewMode,selectDay" class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-sm transition-opacity dark:border-white/10 dark:bg-slate-950/40">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($selectedDay)->format('l, F j, Y') }}</h4>
                <button wire:click="openNewEvent('{{ $selectedDay }}')" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">New on this day</button>
            </div>

            <div class="space-y-3">
                @forelse ($dayEvents as $event)
                    <button wire:key="day-event-{{ $event->id }}" wire:click="openEvent('{{ $event->id }}')" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-left shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900/40 {{ $event->start_at && $event->start_at->isPast() ? 'opacity-70' : '' }}">
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

    <x-crm.card class="p-5 shadow-sm">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Upcoming 7 Days</h4>
        @livewire(\Modules\Calendar\Livewire\UpcomingEvents::class)
    </x-crm.card>

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
