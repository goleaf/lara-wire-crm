<?php

namespace Modules\Calendar\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Calendar\Models\CalendarEvent;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

#[Defer]
class EventForm extends Component
{
    public ?string $eventId = null;

    public string $title = '';

    public string $type = 'Meeting';

    public string $startAt = '';

    public string $endAt = '';

    public bool $allDay = false;

    public string $location = '';

    public string $description = '';

    public string $organizerId = '';

    /**
     * @var array<int, string>
     */
    public array $attendeeIds = [];

    public string $contactId = '';

    public string $dealId = '';

    public string $reminderMinutes = '';

    public string $recurrence = 'None';

    public string $recurrenceEndDate = '';

    public string $status = 'Scheduled';

    public string $color = '#0ea5e9';

    public string $contactSearch = '';

    public string $dealSearch = '';

    public function mount(?string $eventId = null, ?string $selectedDate = null): void
    {
        abort_unless(auth()->user()?->can($eventId ? 'calendar.edit' : 'calendar.create'), 403);

        $this->organizerId = (string) auth()->id();
        $this->eventId = $eventId;

        if ($selectedDate) {
            $this->startAt = Carbon::parse($selectedDate)->setTime(9, 0)->format('Y-m-d\TH:i');
            $this->endAt = Carbon::parse($selectedDate)->setTime(10, 0)->format('Y-m-d\TH:i');
        }

        if (! $eventId) {
            return;
        }

        $event = CalendarEvent::query()
            ->with('attendees:id')
            ->findOrFail($eventId);

        $this->title = (string) $event->title;
        $this->type = (string) $event->type;
        $this->startAt = $event->start_at?->format('Y-m-d\TH:i') ?? '';
        $this->endAt = $event->end_at?->format('Y-m-d\TH:i') ?? '';
        $this->allDay = (bool) $event->all_day;
        $this->location = (string) ($event->location ?? '');
        $this->description = (string) ($event->description ?? '');
        $this->organizerId = (string) $event->organizer_id;
        $this->attendeeIds = $event->attendees->pluck('id')->all();
        $this->contactId = (string) ($event->contact_id ?? '');
        $this->dealId = (string) ($event->deal_id ?? '');
        $this->reminderMinutes = $event->reminder_minutes ? (string) $event->reminder_minutes : '';
        $this->recurrence = (string) $event->recurrence;
        $this->recurrenceEndDate = $event->recurrence_end_date?->toDateString() ?? '';
        $this->status = (string) $event->status;
        $this->color = (string) ($event->color ?? '#0ea5e9');
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $startAt = Carbon::parse($validated['startAt']);
        $endAt = $this->nullableString($validated['endAt']);

        if ($validated['allDay']) {
            $startAt = $startAt->copy()->startOfDay();
            $endAt = null;
        }

        $event = CalendarEvent::query()->updateOrCreate(
            ['id' => $this->eventId],
            [
                'title' => $validated['title'],
                'type' => $validated['type'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'all_day' => $validated['allDay'],
                'location' => $this->nullableString($validated['location']),
                'description' => $this->nullableString($validated['description']),
                'organizer_id' => $validated['organizerId'],
                'contact_id' => $this->nullableString($validated['contactId']),
                'deal_id' => $this->nullableString($validated['dealId']),
                'reminder_minutes' => $this->nullableInt($validated['reminderMinutes']),
                'recurrence' => $validated['recurrence'],
                'recurrence_end_date' => $this->nullableString($validated['recurrenceEndDate']),
                'status' => $validated['status'],
                'color' => $this->nullableString($validated['color']),
            ],
        );

        $event->attendees()->sync($validated['attendeeIds']);

        $this->dispatch('calendar-event-saved', eventId: $event->id);
    }

    public function render(): View
    {
        $users = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $contacts = class_exists(Contact::class)
            ? Contact::query()
                ->select(['id', 'first_name', 'last_name'])
                ->when($this->contactSearch !== '', function (Builder $query): void {
                    $query->where(function (Builder $subQuery): void {
                        $subQuery
                            ->where('first_name', 'like', '%'.$this->contactSearch.'%')
                            ->orWhere('last_name', 'like', '%'.$this->contactSearch.'%');
                    });
                })
                ->orderBy('first_name')
                ->limit(20)
                ->get()
            : collect();

        $deals = class_exists(Deal::class)
            ? Deal::query()
                ->select(['id', 'name'])
                ->when($this->dealSearch !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$this->dealSearch.'%'))
                ->orderBy('name')
                ->limit(20)
                ->get()
            : collect();

        return view('calendar::livewire.event-form', [
            'colors' => ['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'],
            'contacts' => $contacts,
            'deals' => $deals,
            'users' => $users,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Meeting', 'Demo', 'Follow-up', 'Reminder', 'Other'])],
            'startAt' => ['required', 'date'],
            'endAt' => ['nullable', 'date', 'after_or_equal:startAt'],
            'allDay' => ['boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'organizerId' => ['required', 'uuid', 'exists:users,id'],
            'attendeeIds' => ['array'],
            'attendeeIds.*' => ['uuid', 'exists:users,id'],
            'contactId' => ['nullable', 'uuid', 'exists:contacts,id'],
            'dealId' => ['nullable', 'uuid', 'exists:deals,id'],
            'reminderMinutes' => ['nullable', Rule::in(['5', '15', '30', '60', '1440'])],
            'recurrence' => ['required', Rule::in(['None', 'Daily', 'Weekly', 'Monthly'])],
            'recurrenceEndDate' => ['nullable', 'date', 'after_or_equal:startAt'],
            'status' => ['required', Rule::in(['Scheduled', 'Completed', 'Cancelled'])],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }

    protected function nullableInt(string $value): ?int
    {
        return $value !== '' ? (int) $value : null;
    }
}
