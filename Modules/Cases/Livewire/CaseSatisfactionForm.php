<?php

namespace Modules\Cases\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Cases\Models\SupportCase;

#[Defer]
class CaseSatisfactionForm extends Component
{
    public string $caseId = '';

    public int $score = 0;

    public string $comment = '';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('cases.view'), 403);

        $this->caseId = $id;

        $supportCase = SupportCase::query()
            ->select(['id', 'satisfaction_score'])
            ->findOrFail($id);

        $this->score = (int) ($supportCase->satisfaction_score ?? 0);
    }

    public function setScore(int $score): void
    {
        if ($score < 1 || $score > 5) {
            return;
        }

        $this->score = $score;
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        $validated = $this->validate([
            'score' => ['required', Rule::in([1, 2, 3, 4, 5])],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $supportCase = SupportCase::query()
            ->select(['id', 'resolution_notes'])
            ->findOrFail($this->caseId);

        $supportCase->forceFill([
            'satisfaction_score' => (int) $validated['score'],
            'resolution_notes' => filled($validated['comment'])
                ? trim((string) $supportCase->resolution_notes."\n\nCSAT: ".$validated['comment'])
                : $supportCase->resolution_notes,
        ])->save();

        session()->flash('status', 'Satisfaction score saved.');
    }

    public function render(): View
    {
        return view('cases::livewire.case-satisfaction-form');
    }
}
