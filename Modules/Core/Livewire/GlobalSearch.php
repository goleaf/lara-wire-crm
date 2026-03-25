<?php

namespace Modules\Core\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

#[Defer]
class GlobalSearch extends Component
{
    public string $query = '';

    public bool $open = false;

    /**
     * @var array<string, array<int, array{title: string, subtitle: string, url: string}>>
     */
    public array $results = [];

    public function openSearch(): void
    {
        $this->open = true;
    }

    public function closeSearch(): void
    {
        $this->open = false;
        $this->query = '';
        $this->results = [];
    }

    public function updatedQuery(): void
    {
        $term = trim($this->query);

        if (mb_strlen($term) < 2) {
            $this->results = [];

            return;
        }

        $this->results = array_filter([
            'Contacts' => $this->searchContacts($term),
            'Accounts' => $this->searchAccounts($term),
            'Deals' => $this->searchDeals($term),
            'Leads' => $this->searchLeads($term),
            'Cases' => $this->searchCases($term),
            'Campaigns' => $this->searchCampaigns($term),
        ]);
    }

    public function render(): View
    {
        return view('core::livewire.global-search');
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchContacts(string $term): array
    {
        if (! class_exists(Contact::class) || ! \Route::has('contacts.show')) {
            return [];
        }

        return Contact::query()
            ->select(['id', 'first_name', 'last_name', 'email', 'phone'])
            ->where(function ($query) use ($term): void {
                $query
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit(3)
            ->get()
            ->map(fn ($contact): array => [
                'title' => trim($contact->first_name.' '.$contact->last_name),
                'subtitle' => (string) ($contact->email ?: $contact->phone ?: 'Contact'),
                'url' => route('contacts.show', ['id' => $contact->id]),
            ])
            ->all();
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchAccounts(string $term): array
    {
        if (! class_exists(Account::class) || ! \Route::has('accounts.show')) {
            return [];
        }

        return Account::query()
            ->select(['id', 'name', 'industry', 'type'])
            ->where('name', 'like', "%{$term}%")
            ->limit(3)
            ->get()
            ->map(fn ($account): array => [
                'title' => (string) $account->name,
                'subtitle' => trim((string) ($account->industry.' • '.$account->type), ' •'),
                'url' => route('accounts.show', ['id' => $account->id]),
            ])
            ->all();
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchDeals(string $term): array
    {
        if (! class_exists(Deal::class) || ! \Route::has('deals.show')) {
            return [];
        }

        return Deal::query()
            ->select(['id', 'name', 'amount', 'currency'])
            ->where('name', 'like', "%{$term}%")
            ->limit(3)
            ->get()
            ->map(fn ($deal): array => [
                'title' => (string) $deal->name,
                'subtitle' => number_format((float) $deal->amount, 2).' '.$deal->currency,
                'url' => route('deals.show', ['id' => $deal->id]),
            ])
            ->all();
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchLeads(string $term): array
    {
        if (! class_exists(Lead::class) || ! \Route::has('leads.show')) {
            return [];
        }

        return Lead::query()
            ->select(['id', 'first_name', 'last_name', 'company', 'status'])
            ->where(function ($query) use ($term): void {
                $query
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('company', 'like', "%{$term}%");
            })
            ->limit(3)
            ->get()
            ->map(fn ($lead): array => [
                'title' => trim($lead->first_name.' '.$lead->last_name),
                'subtitle' => trim((string) ($lead->company.' • '.$lead->status), ' •'),
                'url' => route('leads.show', ['id' => $lead->id]),
            ])
            ->all();
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchCases(string $term): array
    {
        if (! class_exists(SupportCase::class) || ! \Route::has('cases.show')) {
            return [];
        }

        return SupportCase::query()
            ->select(['id', 'number', 'title', 'status'])
            ->where(function ($query) use ($term): void {
                $query
                    ->where('number', 'like', "%{$term}%")
                    ->orWhere('title', 'like', "%{$term}%");
            })
            ->limit(3)
            ->get()
            ->map(fn ($supportCase): array => [
                'title' => (string) $supportCase->number,
                'subtitle' => trim((string) ($supportCase->title.' • '.$supportCase->status), ' •'),
                'url' => route('cases.show', ['id' => $supportCase->id]),
            ])
            ->all();
    }

    /**
     * @return array<int, array{title: string, subtitle: string, url: string}>
     */
    protected function searchCampaigns(string $term): array
    {
        if (! class_exists(Campaign::class) || ! \Route::has('campaigns.show')) {
            return [];
        }

        return Campaign::query()
            ->select(['id', 'name', 'type', 'status'])
            ->where('name', 'like', "%{$term}%")
            ->limit(3)
            ->get()
            ->map(fn ($campaign): array => [
                'title' => (string) $campaign->name,
                'subtitle' => trim((string) ($campaign->type.' • '.$campaign->status), ' •'),
                'url' => route('campaigns.show', ['id' => $campaign->id]),
            ])
            ->all();
    }
}
