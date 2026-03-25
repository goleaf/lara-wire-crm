<?php

namespace Modules\Activities\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class ActivityForm extends Component
{
    public ?string $activityId = null;

    public string $type = 'Task';

    public string $subject = '';

    public string $description = '';

    public string $status = 'Planned';

    public string $priority = 'Normal';

    public string $dueDate = '';

    public ?int $durationMinutes = null;

    public string $outcome = '';

    public string $relatedType = '';

    public string $relatedId = '';

    public string $ownerId = '';

    /**
     * @var array<int, string>
     */
    public array $attendeeIds = [];

    public string $reminderAt = '';

    public string $relatedSearch = '';

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()?->can($id ? 'activities.edit' : 'activities.create'), 403);

        $this->ownerId = (string) auth()->id();
        $this->prefillFromRequest();

        if (! $id) {
            return;
        }

        $activity = Activity::query()
            ->with('attendees:id')
            ->findOrFail($id);

        $this->activityId = $activity->id;
        $this->type = (string) $activity->type;
        $this->subject = (string) $activity->subject;
        $this->description = (string) ($activity->description ?? '');
        $this->status = (string) $activity->status;
        $this->priority = (string) $activity->priority;
        $this->dueDate = $activity->due_date?->format('Y-m-d\TH:i') ?? '';
        $this->durationMinutes = $activity->duration_minutes;
        $this->outcome = (string) ($activity->outcome ?? '');
        $this->relatedType = $this->resolveRelatedKey((string) ($activity->related_to_type ?? ''));
        $this->relatedId = (string) ($activity->related_to_id ?? '');
        $this->ownerId = (string) $activity->owner_id;
        $this->attendeeIds = $activity->attendees->pluck('id')->all();
        $this->reminderAt = $activity->reminder_at?->format('Y-m-d\TH:i') ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $relatedClass = $this->relatedTypeMap()[$this->relatedType] ?? null;
        $relatedId = $this->nullableString($validated['relatedId']);

        if ($relatedClass && $relatedId) {
            $relatedExists = $relatedClass::query()
                ->whereKey($relatedId)
                ->exists();

            if (! $relatedExists) {
                $this->addError('relatedId', 'Selected related record does not exist.');

                return;
            }
        }

        $activity = Activity::query()->updateOrCreate(
            ['id' => $this->activityId],
            [
                'type' => $validated['type'],
                'subject' => $validated['subject'],
                'description' => $this->nullableString($validated['description']),
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'due_date' => $this->nullableString($validated['dueDate']),
                'duration_minutes' => $validated['durationMinutes'],
                'outcome' => $this->nullableString($validated['outcome']),
                'related_to_type' => $relatedClass,
                'related_to_id' => $relatedClass ? $relatedId : null,
                'owner_id' => $validated['ownerId'],
                'reminder_at' => $this->nullableString($validated['reminderAt']),
            ],
        );

        $activity->attendees()->sync($validated['attendeeIds']);

        session()->flash('status', 'Activity saved successfully.');
        $this->redirectRoute('activities.show', ['id' => $activity->id], navigate: true);
    }

    public function applyDurationPreset(int $minutes): void
    {
        $this->durationMinutes = $minutes;
    }

    public function render(): View
    {
        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('activities::livewire.activity-form', [
            'owners' => $owners,
            'relatedRecords' => $this->relatedRecords(),
            'priorities' => ['Low', 'Normal', 'High'],
            'relatedTypes' => [
                'deal' => 'Deal',
                'contact' => 'Contact',
                'account' => 'Account',
                'lead' => 'Lead',
            ],
            'statuses' => ['Planned', 'Completed', 'Cancelled'],
            'types' => ['Meeting', 'Task', 'Note', 'SMS'],
        ])->extends('core::layouts.module', ['title' => $this->activityId ? 'Edit Activity' : 'New Activity']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['Meeting', 'Task', 'Note', 'SMS'])],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Planned', 'Completed', 'Cancelled'])],
            'priority' => ['required', Rule::in(['Low', 'Normal', 'High'])],
            'dueDate' => ['nullable', 'date'],
            'durationMinutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'outcome' => ['nullable', 'string'],
            'relatedType' => ['nullable', Rule::in(array_keys($this->relatedTypeMap()))],
            'relatedId' => ['nullable', 'uuid'],
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
            'attendeeIds' => ['array'],
            'attendeeIds.*' => ['uuid', 'exists:users,id'],
            'reminderAt' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<int, array{id:string,label:string}>
     */
    protected function relatedRecords(): array
    {
        if ($this->relatedType === '') {
            return [];
        }

        $className = $this->relatedTypeMap()[$this->relatedType] ?? null;

        if (! $className) {
            return [];
        }

        if ($this->relatedType === 'deal') {
            return $className::query()
                ->select(['id', 'name'])
                ->when($this->relatedSearch !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$this->relatedSearch.'%'))
                ->orderBy('name')
                ->limit(20)
                ->get()
                ->map(fn ($deal): array => ['id' => (string) $deal->id, 'label' => (string) $deal->name])
                ->all();
        }

        if ($this->relatedType === 'account') {
            return $className::query()
                ->select(['id', 'name'])
                ->when($this->relatedSearch !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$this->relatedSearch.'%'))
                ->orderBy('name')
                ->limit(20)
                ->get()
                ->map(fn ($account): array => ['id' => (string) $account->id, 'label' => (string) $account->name])
                ->all();
        }

        return $className::query()
            ->select(['id', 'first_name', 'last_name'])
            ->when($this->relatedSearch !== '', function (Builder $query): void {
                $query->where(function (Builder $subQuery): void {
                    $subQuery
                        ->where('first_name', 'like', '%'.$this->relatedSearch.'%')
                        ->orWhere('last_name', 'like', '%'.$this->relatedSearch.'%');
                });
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get()
            ->map(fn ($person): array => ['id' => (string) $person->id, 'label' => trim($person->first_name.' '.$person->last_name)])
            ->all();
    }

    protected function prefillFromRequest(): void
    {
        $type = strtolower(request()->string('related_type')->toString());
        $id = request()->string('related_id')->toString();

        if ($type === '' || $id === '') {
            return;
        }

        if (array_key_exists($type, $this->relatedTypeMap())) {
            $this->relatedType = $type;
            $this->relatedId = $id;
        }
    }

    /**
     * @return array<string, string>
     */
    protected function relatedTypeMap(): array
    {
        return array_filter([
            'deal' => class_exists(Deal::class) ? Deal::class : null,
            'contact' => class_exists(Contact::class) ? Contact::class : null,
            'account' => class_exists(Account::class) ? Account::class : null,
            'lead' => class_exists(Lead::class) ? Lead::class : null,
        ]);
    }

    protected function resolveRelatedKey(string $className): string
    {
        $result = array_search($className, $this->relatedTypeMap(), true);

        return is_string($result) ? $result : '';
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
