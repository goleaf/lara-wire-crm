<?php

namespace Modules\Calendar\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Calendar\Services\CalendarService;

#[Defer]
class UpcomingEvents extends Component
{
    public function render(CalendarService $calendarService): View
    {
        $events = $calendarService
            ->getEventsForWeek(now()->copy(), auth()->user())
            ->filter(fn ($event) => $event->start_at->between(now(), now()->addDays(7)))
            ->take(20)
            ->values();

        return view('calendar::livewire.upcoming-events', [
            'events' => $events,
        ]);
    }
}
