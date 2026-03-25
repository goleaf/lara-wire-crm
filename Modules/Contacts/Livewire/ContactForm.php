<?php

namespace Modules\Contacts\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;

class ContactForm extends Component
{
    public ?string $contactId = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $mobile = '';

    public string $jobTitle = '';

    public string $department = '';

    public string $accountId = '';

    public string $ownerId = '';

    public string $leadSource = 'Walk-in';

    public bool $doNotContact = false;

    public string $birthday = '';

    public string $preferredChannel = 'Phone';

    public string $notes = '';

    public string $accountSearch = '';

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()?->can($id ? 'contacts.edit' : 'contacts.create'), 403);

        $this->ownerId = (string) auth()->id();
        $prefilledAccountId = request()->string('account_id')->toString();

        if ($prefilledAccountId !== '') {
            $this->accountId = $prefilledAccountId;
        }

        if (! $id) {
            return;
        }

        $contact = Contact::query()->findOrFail($id);
        $this->contactId = $contact->id;
        $this->firstName = (string) $contact->first_name;
        $this->lastName = (string) $contact->last_name;
        $this->email = (string) ($contact->email ?? '');
        $this->phone = (string) ($contact->phone ?? '');
        $this->mobile = (string) ($contact->mobile ?? '');
        $this->jobTitle = (string) ($contact->job_title ?? '');
        $this->department = (string) ($contact->department ?? '');
        $this->accountId = (string) ($contact->account_id ?? '');
        $this->ownerId = (string) $contact->owner_id;
        $this->leadSource = (string) $contact->lead_source;
        $this->doNotContact = (bool) $contact->do_not_contact;
        $this->birthday = $contact->birthday?->toDateString() ?? '';
        $this->preferredChannel = (string) $contact->preferred_channel;
        $this->notes = (string) ($contact->notes ?? '');
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $contact = Contact::query()->updateOrCreate(
            ['id' => $this->contactId],
            [
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'email' => $this->nullableString($validated['email']),
                'phone' => $this->nullableString($validated['phone']),
                'mobile' => $this->nullableString($validated['mobile']),
                'job_title' => $this->nullableString($validated['jobTitle']),
                'department' => $this->nullableString($validated['department']),
                'account_id' => $this->nullableString($validated['accountId']),
                'owner_id' => $validated['ownerId'],
                'lead_source' => $validated['leadSource'],
                'do_not_contact' => $validated['doNotContact'],
                'birthday' => $this->nullableString($validated['birthday']),
                'preferred_channel' => $validated['preferredChannel'],
                'notes' => $this->nullableString($validated['notes']),
            ],
        );

        session()->flash('status', 'Contact saved successfully.');
        $this->redirectRoute('contacts.show', ['id' => $contact->id], navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'jobTitle' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'accountId' => ['nullable', 'uuid', 'exists:accounts,id'],
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
            'leadSource' => ['required', Rule::in($this->leadSources())],
            'doNotContact' => ['boolean'],
            'birthday' => ['nullable', 'date'],
            'preferredChannel' => ['required', Rule::in(['Phone', 'SMS', 'In-person'])],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function render(): View
    {
        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $accounts = Account::query()
            ->select(['id', 'name'])
            ->when($this->accountSearch !== '', fn ($query) => $query->where('name', 'like', '%'.$this->accountSearch.'%'))
            ->orderBy('name')
            ->limit(25)
            ->get();

        return view('contacts::livewire.contact-form', [
            'accounts' => $accounts,
            'leadSources' => $this->leadSources(),
            'owners' => $owners,
        ])->extends('core::layouts.module', [
            'title' => $this->contactId ? 'Edit Contact' : 'New Contact',
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function leadSources(): array
    {
        return ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'];
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
