<?php

namespace Modules\Campaigns\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;
use Modules\Contacts\Models\Contact;

class CampaignDetail extends Component
{
    public string $campaignId = '';

    /**
     * @var array<int, string>
     */
    public array $selectedContactIds = [];

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('campaigns.view'), 403);
        $this->campaignId = $id;
    }

    public function addContacts(): void
    {
        abort_unless(auth()->user()?->can('campaigns.create'), 403);

        $validated = $this->validate([
            'selectedContactIds' => ['required', 'array', 'min:1'],
            'selectedContactIds.*' => ['uuid', Rule::exists('contacts', 'id')],
        ]);

        $campaign = Campaign::query()->findOrFail($this->campaignId);
        $payload = [];

        foreach ($validated['selectedContactIds'] as $contactId) {
            $payload[$contactId] = [
                'added_at' => now(),
                'status' => 'Targeted',
            ];
        }

        $campaign->contacts()->syncWithoutDetaching($payload);
        $this->selectedContactIds = [];

        session()->flash('status', 'Contacts added.');
    }

    public function updateContactStatus(string $contactId, string $status): void
    {
        abort_unless(auth()->user()?->can('campaigns.edit'), 403);

        if (! in_array($status, ['Targeted', 'Contacted', 'Responded', 'Converted', 'Opted Out'], true)) {
            return;
        }

        $campaign = Campaign::query()->findOrFail($this->campaignId);

        $campaign->contacts()->updateExistingPivot($contactId, [
            'status' => $status,
        ]);

        session()->flash('status', 'Contact status updated.');
    }

    public function removeContact(string $contactId): void
    {
        abort_unless(auth()->user()?->can('campaigns.delete'), 403);

        $campaign = Campaign::query()->findOrFail($this->campaignId);
        $campaign->contacts()->detach($contactId);

        session()->flash('status', 'Contact removed from campaign.');
    }

    public function render(): View
    {
        $campaign = Campaign::query()
            ->select([
                'id',
                'name',
                'type',
                'status',
                'start_date',
                'end_date',
                'budget',
                'actual_cost',
                'target_audience',
                'expected_leads',
                'description',
                'owner_id',
            ])
            ->with([
                'owner:id,full_name',
                'contacts:id,first_name,last_name,email,owner_id',
                'leads:id,first_name,last_name,status,score,campaign_id,converted,converted_to_deal_id',
            ])
            ->withCount('leads')
            ->findOrFail($this->campaignId);

        $contactStatusCounts = $campaign->contacts
            ->groupBy(fn ($contact) => (string) $contact->pivot->status)
            ->map->count();

        $availableContacts = Contact::query()
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->whereNotIn('id', $campaign->contacts->pluck('id'))
            ->orderBy('first_name')
            ->limit(100)
            ->get();

        return view('campaigns::livewire.campaign-detail', [
            'availableContacts' => $availableContacts,
            'campaign' => $campaign,
            'contactStatusCounts' => $contactStatusCounts,
            'contactStatuses' => ['Targeted', 'Contacted', 'Responded', 'Converted', 'Opted Out'],
        ])->extends('core::layouts.module', ['title' => $campaign->name]);
    }
}
