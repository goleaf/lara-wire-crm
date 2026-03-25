<?php

namespace Modules\Cases\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Cases\Models\SupportCase;

class CaseDetail extends Component
{
    public string $caseId = '';

    public string $tab = 'comments';

    public string $commentBody = '';

    public bool $commentInternal = false;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('cases.view'), 403);
        $this->caseId = $id;
    }

    public function addComment(): void
    {
        abort_unless(auth()->user()?->can('cases.create'), 403);

        $validated = $this->validate([
            'commentBody' => ['required', 'string'],
            'commentInternal' => ['boolean'],
        ]);

        $supportCase = SupportCase::query()
            ->select(['id'])
            ->findOrFail($this->caseId);

        $supportCase->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['commentBody'],
            'is_internal' => (bool) $validated['commentInternal'],
        ]);

        $this->commentBody = '';
        $this->commentInternal = false;

        session()->flash('status', 'Comment added.');
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['comments', 'activities', 'files', 'linked'], true)) {
            return;
        }

        $this->tab = $tab;
    }

    public function changeStatus(string $status): void
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        $allowed = ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'];

        if (! in_array($status, $allowed, true)) {
            return;
        }

        $supportCase = SupportCase::query()
            ->select(['id', 'status', 'resolved_at', 'closed_at'])
            ->findOrFail($this->caseId);

        $supportCase->status = $status;
        $supportCase->save();

        session()->flash('status', 'Status updated.');
    }

    public function saveSatisfaction(int $score, ?string $comment = null): void
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        validator(
            ['score' => $score, 'comment' => $comment],
            ['score' => ['required', Rule::in([1, 2, 3, 4, 5])], 'comment' => ['nullable', 'string']]
        )->validate();

        $supportCase = SupportCase::query()
            ->select(['id', 'resolution_notes'])
            ->findOrFail($this->caseId);

        $supportCase->forceFill([
            'satisfaction_score' => $score,
            'resolution_notes' => filled($comment)
                ? trim((string) $supportCase->resolution_notes."\n\nCSAT: ".$comment)
                : $supportCase->resolution_notes,
        ])->save();

        session()->flash('status', 'Satisfaction saved.');
    }

    public function render(): View
    {
        $supportCase = SupportCase::query()
            ->select([
                'id',
                'number',
                'title',
                'description',
                'status',
                'priority',
                'type',
                'contact_id',
                'account_id',
                'deal_id',
                'owner_id',
                'sla_deadline',
                'first_response_at',
                'resolved_at',
                'closed_at',
                'satisfaction_score',
                'channel',
                'resolution_notes',
                'created_at',
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name,email',
                'deal:id,name',
                'owner:id,full_name',
                'comments:id,case_id,user_id,body,is_internal,created_at',
                'comments.user:id,full_name',
            ])
            ->findOrFail($this->caseId);

        return view('cases::livewire.case-detail', [
            'supportCase' => $supportCase,
            'statusFlow' => ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'],
        ])->extends('core::layouts.module', ['title' => $supportCase->number]);
    }
}
