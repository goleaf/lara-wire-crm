<?php

namespace Modules\Activities\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Activities\Models\Activity;

#[Defer]
class QuickAddActivity extends Component
{
    public bool $isOpen = false;

    public string $type = 'Task';

    public string $subject = '';

    public string $dueDate = '';

    public string $ownerId = '';

    public string $relatedType = '';

    public string $relatedId = '';

    public function mount(): void
    {
        $this->ownerId = (string) auth()->id();
    }

    #[On('quick-add-activity')]
    public function open(array $context = []): void
    {
        abort_unless(auth()->user()?->can('activities.create'), 403);

        $this->isOpen = true;
        $this->relatedType = (string) ($context['relatedType'] ?? '');
        $this->relatedId = (string) ($context['relatedId'] ?? '');
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('activities.create'), 403);

        $validated = $this->validate([
            'type' => ['required', Rule::in(['Meeting', 'Task', 'Note', 'SMS'])],
            'subject' => ['required', 'string', 'max:255'],
            'dueDate' => ['nullable', 'date'],
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
        ]);

        Activity::query()->create([
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'status' => 'Planned',
            'priority' => 'Normal',
            'due_date' => $this->nullableString($validated['dueDate']),
            'owner_id' => $validated['ownerId'],
            'related_to_type' => $this->nullableString($this->relatedType),
            'related_to_id' => $this->nullableString($this->relatedId),
        ]);

        $this->isOpen = false;
        $this->subject = '';
        $this->dueDate = '';
        $this->relatedType = '';
        $this->relatedId = '';

        $this->dispatch('flash', [
            'type' => 'success',
            'message' => 'Activity added.',
        ]);
        $this->dispatch('activity-created');
    }

    public function render(): View
    {
        return view('activities::livewire.quick-add-activity');
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
