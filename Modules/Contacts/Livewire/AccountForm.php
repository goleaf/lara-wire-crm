<?php

namespace Modules\Contacts\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Contacts\Models\Account;

class AccountForm extends Component
{
    public ?string $accountId = null;

    public string $name = '';

    public string $industry = 'Technology';

    public string $type = 'Customer';

    public string $website = '';

    public string $phone = '';

    public string $email = '';

    public ?float $annualRevenue = null;

    public ?int $employeeCount = null;

    public string $ownerId = '';

    public string $parentAccountId = '';

    public string $tagsInput = '';

    /**
     * @var array{street:string,city:string,state:string,zip:string,country:string}
     */
    public array $billingAddress = [
        'street' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
    ];

    /**
     * @var array{street:string,city:string,state:string,zip:string,country:string}
     */
    public array $shippingAddress = [
        'street' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
    ];

    public bool $sameAsBilling = false;

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()?->can($id ? 'contacts.edit' : 'contacts.create'), 403);

        $this->ownerId = (string) auth()->id();

        if (! $id) {
            return;
        }

        $account = Account::query()->findOrFail($id);
        $this->accountId = $account->id;
        $this->name = (string) $account->name;
        $this->industry = (string) $account->industry;
        $this->type = (string) $account->type;
        $this->website = (string) ($account->website ?? '');
        $this->phone = (string) ($account->phone ?? '');
        $this->email = (string) ($account->email ?? '');
        $this->annualRevenue = $account->annual_revenue !== null ? (float) $account->annual_revenue : null;
        $this->employeeCount = $account->employee_count;
        $this->ownerId = (string) $account->owner_id;
        $this->parentAccountId = (string) ($account->parent_account_id ?? '');
        $this->tagsInput = implode(', ', $account->tags ?? []);
        $this->billingAddress = array_merge($this->billingAddress, $account->billing_address ?? []);
        $this->shippingAddress = array_merge($this->shippingAddress, $account->shipping_address ?? []);
    }

    public function updatedSameAsBilling(bool $value): void
    {
        if ($value) {
            $this->shippingAddress = $this->billingAddress;
        }
    }

    public function save(string $mode = 'continue'): void
    {
        $validated = $this->validate($this->rules());

        if ($this->sameAsBilling) {
            $this->shippingAddress = $this->billingAddress;
        }

        $payload = [
            'name' => $validated['name'],
            'industry' => $validated['industry'],
            'type' => $validated['type'],
            'website' => $this->nullableString($validated['website']),
            'phone' => $this->nullableString($validated['phone']),
            'email' => $this->nullableString($validated['email']),
            'billing_address' => $validated['billingAddress'],
            'shipping_address' => $this->isAddressFilled($validated['shippingAddress']) ? $validated['shippingAddress'] : null,
            'annual_revenue' => $validated['annualRevenue'],
            'employee_count' => $validated['employeeCount'],
            'owner_id' => $validated['ownerId'],
            'parent_account_id' => $this->nullableString($validated['parentAccountId']),
            'tags' => $this->parsedTags(),
        ];

        $account = Account::query()->updateOrCreate(
            ['id' => $this->accountId],
            $payload,
        );

        session()->flash('status', 'Account saved successfully.');

        if ($mode === 'new') {
            $this->redirectRoute('accounts.create', navigate: true);

            return;
        }

        if ($mode === 'continue') {
            $this->redirectRoute('accounts.show', ['id' => $account->id], navigate: true);

            return;
        }

        $this->redirectRoute('accounts.index', navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['required', Rule::in($this->industries())],
            'type' => ['required', Rule::in($this->types())],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'billingAddress.street' => ['required', 'string', 'max:255'],
            'billingAddress.city' => ['required', 'string', 'max:120'],
            'billingAddress.state' => ['required', 'string', 'max:120'],
            'billingAddress.zip' => ['required', 'string', 'max:40'],
            'billingAddress.country' => ['required', 'string', 'max:120'],
            'shippingAddress.street' => ['nullable', 'string', 'max:255'],
            'shippingAddress.city' => ['nullable', 'string', 'max:120'],
            'shippingAddress.state' => ['nullable', 'string', 'max:120'],
            'shippingAddress.zip' => ['nullable', 'string', 'max:40'],
            'shippingAddress.country' => ['nullable', 'string', 'max:120'],
            'annualRevenue' => ['nullable', 'numeric', 'min:0'],
            'employeeCount' => ['nullable', 'integer', 'min:0'],
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
            'parentAccountId' => ['nullable', 'uuid', 'exists:accounts,id', Rule::notIn(array_filter([$this->accountId]))],
        ];
    }

    public function render(): View
    {
        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $parentAccounts = Account::query()
            ->select(['id', 'name'])
            ->when($this->accountId !== null, fn ($query) => $query->whereKeyNot($this->accountId))
            ->orderBy('name')
            ->limit(25)
            ->get();

        return view('contacts::livewire.account-form', [
            'industries' => $this->industries(),
            'owners' => $owners,
            'parentAccounts' => $parentAccounts,
            'types' => $this->types(),
        ])->extends('core::layouts.module', [
            'title' => $this->accountId ? 'Edit Account' : 'New Account',
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function industries(): array
    {
        return ['Technology', 'Finance', 'Retail', 'Healthcare', 'Manufacturing', 'Education', 'Real Estate', 'Other'];
    }

    /**
     * @return array<int, string>
     */
    protected function types(): array
    {
        return ['Customer', 'Partner', 'Prospect', 'Vendor'];
    }

    /**
     * @return array<int, string>
     */
    protected function parsedTags(): array
    {
        return collect(explode(',', $this->tagsInput))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array{street:?string,city:?string,state:?string,zip:?string,country:?string}  $address
     */
    protected function isAddressFilled(array $address): bool
    {
        return collect($address)->filter(fn (?string $value): bool => filled($value))->isNotEmpty();
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
