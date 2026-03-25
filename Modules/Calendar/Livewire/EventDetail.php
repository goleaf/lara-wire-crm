<?php

namespace Modules\Calendar\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Calendar\Models\CalendarEvent;

#[Defer]
class EventDetail extends Component
{
    public ?string $eventId = null;

    public ?CalendarEvent $event = null;

    public function mount(?string $eventId = null): void
    {
        $this->eventId = $eventId;
        $this->loadEvent();
    }

    public function markComplete(): void
    {
        abort_unless(auth()->user()?->can('calendar.edit'), 403);

        if (! $this->event) {
            return;
        }

        $this->event->update([
            'status' => 'Completed',
        ]);

        $this->loadEvent();
    }

    public function deleteEvent(): void
    {
        abort_unless(auth()->user()?->can('calendar.delete'), 403);

        if (! $this->event) {
            return;
        }

        $this->event->delete();
        $this->dispatch('calendar-event-deleted');
    }

    public function render(): View
    {
        return view('calendar::livewire.event-detail');
    }

    protected function loadEvent(): void
    {
        if (! $this->eventId) {
            $this->event = null;

            return;
        }

        $this->event = CalendarEvent::query()
            ->with([
                'organizer:id,full_name,email',
                'attendees:id,full_name,avatar_path',
                'contact:id,first_name,last_name',
                'deal:id,name',
            ])
            ->find($this->eventId);
    }
}
