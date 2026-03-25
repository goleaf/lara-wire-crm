<?php

namespace Modules\Activities\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Activities\Models\Activity;

class ActivityTimeline extends Component
{
    public string $relatedType;

    public string $relatedId;

    public string $typeFilter = '';

    public string $newType = 'Task';

    public string $newSubject = '';

    public string $newDueDate = '';

    public string $newOwnerId = '';

    public function mount(string $relatedType, string $relatedId): void
    {
        abort_unless(auth()->user()?->can('activities.view'), 403);

        $this->relatedType = $relatedType;
        $this->relatedId = $relatedId;
        $this->newOwnerId = (string) auth()->id();
    }

    public function addActivity(): void
    {
        abort_unless(auth()->user()?->can('activities.create'), 403);

        $validated = $this->validate([
            'newType' => ['required', Rule::in(['Meeting', 'Task', 'Note', 'SMS'])],
            'newSubject' => ['required', 'string', 'max:255'],
            'newDueDate' => ['nullable', 'date'],
            'newOwnerId' => ['required', 'uuid', 'exists:users,id'],
        ]);

        Activity::query()->create([
            'type' => $validated['newType'],
            'subject' => $validated['newSubject'],
            'status' => 'Planned',
            'priority' => 'Normal',
            'due_date' => $this->nullableString($validated['newDueDate']),
            'related_to_type' => $this->relatedType,
            'related_to_id' => $this->relatedId,
            'owner_id' => $validated['newOwnerId'],
        ]);

        $this->newSubject = '';
        $this->newDueDate = '';
        session()->flash('status', 'Activity added.');
    }

    public function markComplete(string $id): void
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        Activity::query()
            ->whereKey($id)
            ->where('related_to_type', $this->relatedType)
            ->where('related_to_id', $this->relatedId)
            ->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
    }

    public function render(): View
    {
        $activities = Activity::query()
            ->select([
                'id',
                'type',
                'subject',
                'status',
                'priority',
                'description',
                'due_date',
                'owner_id',
                'related_to_type',
                'related_to_id',
            ])
            ->where('related_to_type', $this->relatedType)
            ->where('related_to_id', $this->relatedId)
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->with('owner:id,full_name')
            ->orderByDesc('due_date')
            ->orderByDesc('created_at')
            ->get();

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('activities::livewire.activity-timeline', [
            'activities' => $activities,
            'owners' => $owners,
            'types' => ['Meeting', 'Task', 'Note', 'SMS'],
        ]);
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
