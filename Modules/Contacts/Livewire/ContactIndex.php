<?php

namespace Modules\Contacts\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;

class ContactIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $accountFilter = '';

    public string $ownerFilter = '';

    public string $doNotContactFilter = '';

    public string $leadSourceFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('contacts.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDoNotContactFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLeadSourceFilter(): void
    {
        $this->resetPage();
    }

    public function deleteContact(string $id): void
    {
        abort_unless(auth()->user()?->can('contacts.delete'), 403);

        Contact::query()->whereKey($id)->delete();
        session()->flash('status', 'Contact deleted.');

        $this->resetPage();
    }

    public function render(): View
    {
        $contacts = Contact::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'account_id',
                'job_title',
                'email',
                'phone',
                'owner_id',
                'preferred_channel',
                'do_not_contact',
                'lead_source',
            ])
            ->with([
                'account:id,name,type',
                'owner:id,full_name',
            ])
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->accountFilter !== '', fn ($query) => $query->where('account_id', $this->accountFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->doNotContactFilter !== '', fn ($query) => $query->where('do_not_contact', $this->doNotContactFilter === '1'))
            ->when($this->leadSourceFilter !== '', fn ($query) => $query->where('lead_source', $this->leadSourceFilter))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15);

        $accounts = Account::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('contacts::livewire.contact-index', [
            'accounts' => $accounts,
            'contacts' => $contacts,
            'leadSources' => ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'],
            'owners' => $owners,
        ])->extends('core::layouts.module', ['title' => 'Contacts']);
    }
}
