<?php

namespace Modules\Cases\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Cases\Models\SlaPolicy;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

class CaseForm extends Component
{
    public ?string $caseId = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'Open';

    public string $priority = 'Medium';

    public string $type = 'Other';

    public string $contact_id = '';

    public string $account_id = '';

    public string $deal_id = '';

    public string $owner_id = '';

    public string $channel = 'Other';

    public string $resolution_notes = '';

    public ?string $sla_preview = null;

    public function mount(?string $id = null): void
    {
        $this->caseId = $id;
        $this->owner_id = (string) auth()->id();

        $prefilledContactId = request()->string('contact_id')->toString();
        $prefilledAccountId = request()->string('account_id')->toString();
        $prefilledDealId = request()->string('deal_id')->toString();

        if ($prefilledContactId !== '') {
            $this->contact_id = $prefilledContactId;
        }
        if ($prefilledAccountId !== '') {
            $this->account_id = $prefilledAccountId;
        }
        if ($prefilledDealId !== '') {
            $this->deal_id = $prefilledDealId;
        }

        if ($this->contact_id !== '' && $this->account_id === '') {
            $accountId = Contact::query()
                ->select(['id', 'account_id'])
                ->whereKey($this->contact_id)
                ->value('account_id');

            if ($accountId !== null) {
                $this->account_id = (string) $accountId;
            }
        }

        if ($this->caseId !== null) {
            abort_unless(auth()->user()?->can('cases.edit'), 403);
            $this->loadCase();
            $this->refreshSlaPreview();

            return;
        }

        abort_unless(auth()->user()?->can('cases.create'), 403);
        $this->refreshSlaPreview();
    }

    public function updatedPriority(): void
    {
        $this->refreshSlaPreview();
    }

    public function updatedContactId(string $contactId): void
    {
        if (blank($contactId) || filled($this->account_id)) {
            return;
        }

        $accountId = Contact::query()
            ->select(['id', 'account_id'])
            ->whereKey($contactId)
            ->value('account_id');

        if ($accountId !== null) {
            $this->account_id = (string) $accountId;
        }
    }

    public function save(): void
    {
        $existingPriority = $this->caseId !== null
            ? (string) (SupportCase::query()->whereKey($this->caseId)->value('priority') ?? '')
            : null;

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'])],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'type' => ['required', Rule::in(['Bug', 'Feature Request', 'Question', 'Complaint', 'Other'])],
            'contact_id' => ['nullable', Rule::exists('contacts', 'id')],
            'account_id' => ['nullable', Rule::exists('accounts', 'id')],
            'deal_id' => ['nullable', Rule::exists('deals', 'id')],
            'owner_id' => ['required', Rule::exists('users', 'id')],
            'channel' => ['required', Rule::in(['Phone', 'In-person', 'Internal Portal', 'Other'])],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $supportCase = SupportCase::query()->updateOrCreate(
            ['id' => $this->caseId],
            [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'type' => $validated['type'],
                'contact_id' => $this->nullableString($validated['contact_id']),
                'account_id' => $this->nullableString($validated['account_id']),
                'deal_id' => $this->nullableString($validated['deal_id']),
                'owner_id' => $validated['owner_id'],
                'channel' => $validated['channel'],
                'resolution_notes' => $this->nullableString($validated['resolution_notes']),
            ]
        );

        if ($existingPriority !== null && $existingPriority !== $validated['priority']) {
            $supportCase->assignSla();
            $supportCase->saveQuietly();
        }

        session()->flash('status', 'Case saved.');
        $this->redirectRoute('cases.show', ['id' => $supportCase->getKey()], navigate: true);
    }

    public function render(): View
    {
        return view('cases::livewire.case-form', [
            'accounts' => Account::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'channels' => ['Phone', 'In-person', 'Internal Portal', 'Other'],
            'contacts' => Contact::query()->select(['id', 'first_name', 'last_name'])->orderBy('first_name')->limit(100)->get(),
            'deals' => Deal::query()->select(['id', 'name'])->orderBy('name')->limit(100)->get(),
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'priorities' => ['Low', 'Medium', 'High', 'Critical'],
            'statuses' => ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'],
            'types' => ['Bug', 'Feature Request', 'Question', 'Complaint', 'Other'],
        ])->extends('core::layouts.module', [
            'title' => $this->caseId ? 'Edit Case' : 'Create Case',
        ]);
    }

    protected function loadCase(): void
    {
        $supportCase = SupportCase::query()
            ->select([
                'id',
                'title',
                'description',
                'status',
                'priority',
                'type',
                'contact_id',
                'account_id',
                'deal_id',
                'owner_id',
                'channel',
                'resolution_notes',
            ])
            ->findOrFail($this->caseId);

        $this->title = (string) $supportCase->title;
        $this->description = (string) $supportCase->description;
        $this->status = (string) $supportCase->status;
        $this->priority = (string) $supportCase->priority;
        $this->type = (string) $supportCase->type;
        $this->contact_id = (string) ($supportCase->contact_id ?? '');
        $this->account_id = (string) ($supportCase->account_id ?? '');
        $this->deal_id = (string) ($supportCase->deal_id ?? '');
        $this->owner_id = (string) $supportCase->owner_id;
        $this->channel = (string) $supportCase->channel;
        $this->resolution_notes = (string) ($supportCase->resolution_notes ?? '');
    }

    protected function refreshSlaPreview(): void
    {
        $policy = SlaPolicy::forPriority($this->priority);

        $this->sla_preview = $policy
            ? now()->addHours((int) $policy->resolution_hours)->format('Y-m-d H:i')
            : null;
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
