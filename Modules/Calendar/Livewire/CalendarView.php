<?php

namespace Modules\Calendar\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Calendar\Services\CalendarService;

class CalendarView extends Component
{
    public string $view = 'month';

    public string $currentDate = '';

    public string $selectedDay = '';

    public bool $showEventForm = false;

    public bool $showEventDetail = false;

    public ?string $selectedEventId = null;

    public ?string $editingEventId = null;

    public string $eventFormDate = '';

    protected CalendarService $calendarService;

    public function boot(CalendarService $calendarService): void
    {
        $this->calendarService = $calendarService;
    }

    public function mount(?string $view = null, ?string $date = null): void
    {
        abort_unless(auth()->user()?->can('calendar.view'), 403);

        $this->view = in_array((string) $view, ['month', 'week', 'day'], true) ? (string) $view : 'month';
        $anchorDate = $date ?: now()->toDateString();
        $this->currentDate = Carbon::parse($anchorDate)->toDateString();
        $this->selectedDay = $this->currentDate;
    }

    public function setViewMode(string $mode): void
    {
        if (! in_array($mode, ['month', 'week', 'day'], true)) {
            return;
        }

        $this->view = $mode;
    }

    public function previousPeriod(): void
    {
        $current = Carbon::parse($this->currentDate);

        $this->currentDate = match ($this->view) {
            'week' => $current->subWeek()->toDateString(),
            'day' => $current->subDay()->toDateString(),
            default => $current->subMonth()->toDateString(),
        };
    }

    public function nextPeriod(): void
    {
        $current = Carbon::parse($this->currentDate);

        $this->currentDate = match ($this->view) {
            'week' => $current->addWeek()->toDateString(),
            'day' => $current->addDay()->toDateString(),
            default => $current->addMonth()->toDateString(),
        };
    }

    public function goToday(): void
    {
        $this->currentDate = now()->toDateString();
        $this->selectedDay = $this->currentDate;
    }

    public function selectDay(string $date): void
    {
        $this->selectedDay = Carbon::parse($date)->toDateString();
        $this->view = 'day';
    }

    public function openNewEvent(?string $date = null): void
    {
        abort_unless(auth()->user()?->can('calendar.create'), 403);

        $this->editingEventId = null;
        $this->eventFormDate = $date ?: $this->selectedDay;
        $this->showEventForm = true;
        $this->showEventDetail = false;
    }

    public function openEvent(string $eventId): void
    {
        $this->selectedEventId = $eventId;
        $this->showEventDetail = true;
    }

    public function closeOverlays(): void
    {
        $this->showEventForm = false;
        $this->showEventDetail = false;
        $this->selectedEventId = null;
        $this->editingEventId = null;
    }

    #[On('calendar-event-saved')]
    public function handleEventSaved(string $eventId): void
    {
        $this->showEventForm = false;
        $this->selectedEventId = $eventId;
        $this->showEventDetail = true;
    }

    #[On('calendar-event-deleted')]
    public function handleEventDeleted(): void
    {
        $this->closeOverlays();
    }

    public function render(): View
    {
        $anchor = Carbon::parse($this->currentDate);

        $events = match ($this->view) {
            'week' => $this->calendarService->getEventsForWeek($anchor->copy(), auth()->user()),
            'day' => $this->calendarService->getEventsForDay(Carbon::parse($this->selectedDay), auth()->user()),
            default => $this->calendarService->getEventsForMonth($anchor->year, $anchor->month, auth()->user()),
        };

        $eventsByDate = $events->groupBy(fn ($event) => $event->start_at?->toDateString() ?? '');

        return view('calendar::livewire.calendar-view', [
            'anchor' => $anchor,
            'events' => $events,
            'eventsByDate' => $eventsByDate,
            'monthDays' => $this->monthDays($anchor),
            'weekDays' => $this->weekDays($anchor),
        ])->extends('core::layouts.module', ['title' => 'Calendar']);
    }

    /**
     * @return array<int, Carbon>
     */
    protected function monthDays(Carbon $anchor): array
    {
        $start = $anchor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $days = [];

        for ($i = 0; $i < 42; $i++) {
            $days[] = $start->copy()->addDays($i);
        }

        return $days;
    }

    /**
     * @return array<int, Carbon>
     */
    protected function weekDays(Carbon $anchor): array
    {
        $start = $anchor->copy()->startOfWeek(Carbon::MONDAY);
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $days[] = $start->copy()->addDays($i);
        }

        return $days;
    }
}
