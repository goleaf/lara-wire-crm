<?php

namespace Modules\Deals\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;
use Modules\Products\Models\Product;

class DealForm extends Component
{
    public ?string $dealId = null;

    public string $name = '';

    public string $accountId = '';

    public string $contactId = '';

    public string $ownerId = '';

    public string $pipelineId = '';

    public string $stageId = '';

    public float $amount = 0;

    public string $currency = 'USD';

    public int $probability = 0;

    public float $expectedRevenue = 0;

    public string $closeDate = '';

    public string $dealType = 'New Business';

    public string $source = '';

    public string $accountSearch = '';

    public string $contactSearch = '';

    public string $ownerSearch = '';

    public string $pipelineSearch = '';

    public string $stageSearch = '';

    /**
     * @var array<int, array{product_id:string,product_name:string,quantity:int,unit_price:float,discount:float,total:float}>
     */
    public array $lineItems = [];

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()?->can($id ? 'deals.edit' : 'deals.create'), 403);

        $this->ownerId = (string) auth()->id();
        $this->currency = (string) config('crm.default_currency.code', 'USD');

        $defaultPipeline = Pipeline::query()->select(['id'])->where('is_default', true)->first();
        $this->pipelineId = (string) ($defaultPipeline?->id ?? '');
        $this->syncStageFromPipeline();

        $prefillContact = request()->string('contact_id')->toString();
        $prefillAccount = request()->string('account_id')->toString();

        if ($prefillContact !== '') {
            $this->contactId = $prefillContact;
        }

        if ($prefillAccount !== '') {
            $this->accountId = $prefillAccount;
        }

        if ($this->contactId !== '' && $this->accountId === '') {
            $prefilledContact = Contact::query()
                ->select(['id', 'account_id'])
                ->find($this->contactId);

            $this->accountId = (string) ($prefilledContact?->account_id ?? '');
        }

        if (! $id) {
            $this->lineItems = [$this->blankLineItem()];
            $this->recalculateExpectedRevenue();
            $this->hydrateAutocompleteLabels();

            return;
        }

        $deal = Deal::query()
            ->with('products:id,name')
            ->findOrFail($id);

        $this->dealId = $deal->id;
        $this->name = (string) $deal->name;
        $this->accountId = (string) $deal->account_id;
        $this->contactId = (string) ($deal->contact_id ?? '');
        $this->ownerId = (string) $deal->owner_id;
        $this->pipelineId = (string) $deal->pipeline_id;
        $this->stageId = (string) $deal->stage_id;
        $this->amount = (float) $deal->amount;
        $this->currency = (string) $deal->currency;
        $this->probability = (int) $deal->probability;
        $this->expectedRevenue = (float) $deal->expected_revenue;
        $this->closeDate = $deal->close_date?->toDateString() ?? '';
        $this->dealType = (string) $deal->deal_type;
        $this->source = (string) ($deal->source ?? '');

        $this->lineItems = $deal->products->map(function ($product): array {
            $quantity = (int) $product->pivot->quantity;
            $unitPrice = (float) $product->pivot->unit_price;
            $discount = (float) $product->pivot->discount;

            return [
                'product_id' => (string) $product->id,
                'product_name' => (string) $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'total' => $this->calculateLineTotal($quantity, $unitPrice, $discount),
            ];
        })->values()->all();

        if ($this->lineItems === []) {
            $this->lineItems = [$this->blankLineItem()];
        }

        $this->hydrateAutocompleteLabels();
    }

    public function updatedPipelineId(): void
    {
        $this->syncStageFromPipeline();
        $this->syncPipelineSearchLabel();
        $this->syncStageSearchLabel();
    }

    public function updatedStageId(string $stageId): void
    {
        $stage = PipelineStage::query()
            ->select(['id', 'probability'])
            ->find($stageId);

        if (! $stage) {
            return;
        }

        $this->probability = (int) $stage->probability;
        $this->recalculateExpectedRevenue();
        $this->syncStageSearchLabel();
    }

    public function updatedAmount(): void
    {
        $this->recalculateExpectedRevenue();
    }

    public function updatedProbability(): void
    {
        $this->recalculateExpectedRevenue();
    }

    public function addLineItem(): void
    {
        $this->lineItems[] = $this->blankLineItem();
    }

    public function removeLineItem(int $index): void
    {
        unset($this->lineItems[$index]);
        $this->lineItems = array_values($this->lineItems);

        if ($this->lineItems === []) {
            $this->lineItems[] = $this->blankLineItem();
        }
    }

    public function updatedLineItems(mixed $value = null, ?string $key = null): void
    {
        if (is_string($key) && preg_match('/^(\d+)\.product_name$/', $key, $matches) === 1) {
            $this->syncLineItemProductFromName((int) $matches[1], (string) $value);
        }

        foreach ($this->lineItems as $index => $item) {
            $this->lineItems[$index]['total'] = $this->calculateLineTotal(
                (int) ($item['quantity'] ?? 1),
                (float) ($item['unit_price'] ?? 0),
                (float) ($item['discount'] ?? 0),
            );
        }
    }

    public function updatedAccountSearch(string $value): void
    {
        $account = Account::query()
            ->select(['id', 'name'])
            ->where('name', $value)
            ->first();

        $this->accountId = (string) ($account?->id ?? '');

        if ($this->accountId === '') {
            $this->contactId = '';
            $this->contactSearch = '';

            return;
        }

        if ($this->contactId !== '') {
            $contactBelongsToAccount = Contact::query()
                ->select(['id'])
                ->where('id', $this->contactId)
                ->where('account_id', $this->accountId)
                ->exists();

            if (! $contactBelongsToAccount) {
                $this->contactId = '';
                $this->contactSearch = '';
            }
        }
    }

    public function updatedContactSearch(string $value): void
    {
        $contact = Contact::query()
            ->select(['id', 'first_name', 'last_name', 'account_id'])
            ->when($this->accountId !== '', fn ($query) => $query->where('account_id', $this->accountId))
            ->orderBy('first_name')
            ->limit(200)
            ->get()
            ->first(fn (Contact $contact): bool => $contact->full_name === $value);

        $this->contactId = (string) ($contact?->id ?? '');

        if ($contact && $this->accountId === '' && $contact->account_id) {
            $this->accountId = (string) $contact->account_id;
            $this->syncAccountSearchLabel();
        }
    }

    public function updatedOwnerSearch(string $value): void
    {
        $owner = User::query()
            ->select(['id', 'full_name'])
            ->where('full_name', $value)
            ->first();

        $this->ownerId = (string) ($owner?->id ?? '');
    }

    public function updatedPipelineSearch(string $value): void
    {
        $pipeline = Pipeline::query()
            ->select(['id', 'name'])
            ->where('name', $value)
            ->first();

        $this->pipelineId = (string) ($pipeline?->id ?? '');
    }

    public function updatedStageSearch(string $value): void
    {
        if ($this->pipelineId === '') {
            $this->stageId = '';

            return;
        }

        $stage = PipelineStage::query()
            ->select(['id', 'name', 'probability'])
            ->where('pipeline_id', $this->pipelineId)
            ->orderBy('order')
            ->get()
            ->first(fn (PipelineStage $stage): bool => $this->formatStageLabel($stage) === $value);

        $this->stageId = (string) ($stage?->id ?? '');
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());

        $deal = Deal::query()->updateOrCreate(
            ['id' => $this->dealId],
            [
                'name' => $validated['name'],
                'account_id' => $validated['accountId'],
                'contact_id' => $this->nullableString($validated['contactId']),
                'owner_id' => $validated['ownerId'],
                'pipeline_id' => $validated['pipelineId'],
                'stage_id' => $validated['stageId'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'probability' => $validated['probability'],
                'expected_revenue' => $this->expectedRevenue,
                'close_date' => $this->nullableString($validated['closeDate']),
                'deal_type' => $validated['dealType'],
                'source' => $this->nullableString($validated['source']),
            ],
        );

        $syncPayload = [];

        foreach ($this->lineItems as $item) {
            if (($item['product_id'] ?? '') === '') {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $discount = (float) ($item['discount'] ?? 0);

            $syncPayload[$item['product_id']] = [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'total' => $this->calculateLineTotal($quantity, $unitPrice, $discount),
            ];
        }

        $deal->products()->sync($syncPayload);

        session()->flash('status', 'Deal saved successfully.');
        $this->redirectRoute('deals.show', ['id' => $deal->id], navigate: true);
    }

    public function render(): View
    {
        $contactsQuery = Contact::query()->select(['id', 'first_name', 'last_name', 'account_id'])->orderBy('first_name');

        if ($this->accountId !== '') {
            $contactsQuery->where('account_id', $this->accountId);
        }

        return view('deals::livewire.deal-form', [
            'accounts' => Account::query()->select(['id', 'name'])->orderBy('name')->get(),
            'contacts' => $contactsQuery->limit(100)->get(),
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'pipelines' => Pipeline::query()->select(['id', 'name'])->orderByDesc('is_default')->orderBy('name')->get(),
            'products' => Product::query()->select(['id', 'name', 'unit_price'])->active()->orderBy('name')->get(),
            'stages' => PipelineStage::query()->select(['id', 'name', 'pipeline_id', 'probability'])->where('pipeline_id', $this->pipelineId)->orderBy('order')->get(),
        ])->extends('core::layouts.module', ['title' => $this->dealId ? 'Edit Deal' : 'New Deal']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'accountId' => ['required', 'uuid', 'exists:accounts,id'],
            'contactId' => ['nullable', 'uuid', 'exists:contacts,id'],
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
            'pipelineId' => ['required', 'uuid', 'exists:pipelines,id'],
            'stageId' => ['required', 'uuid', 'exists:pipeline_stages,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
            'closeDate' => ['nullable', 'date'],
            'dealType' => ['required', Rule::in(['New Business', 'Renewal', 'Upsell', 'Cross-sell'])],
            'source' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function syncStageFromPipeline(): void
    {
        if ($this->pipelineId === '') {
            $this->stageId = '';
            $this->probability = 0;
            $this->recalculateExpectedRevenue();

            return;
        }

        $firstStage = PipelineStage::query()
            ->select(['id', 'probability'])
            ->where('pipeline_id', $this->pipelineId)
            ->orderBy('order')
            ->first();

        if (! $firstStage) {
            return;
        }

        if ($this->stageId === '') {
            $this->stageId = (string) $firstStage->id;
        }

        $this->probability = (int) $firstStage->probability;
        $this->recalculateExpectedRevenue();
    }

    protected function recalculateExpectedRevenue(): void
    {
        $this->expectedRevenue = round(($this->amount * $this->probability) / 100, 2);
    }

    /**
     * @return array{product_id:string,product_name:string,quantity:int,unit_price:float,discount:float,total:float}
     */
    protected function blankLineItem(): array
    {
        return [
            'product_id' => '',
            'product_name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'total' => 0,
        ];
    }

    protected function calculateLineTotal(int $quantity, float $unitPrice, float $discount): float
    {
        $subtotal = max(0, $quantity) * max(0, $unitPrice);
        $discounted = $subtotal * (1 - (max(0, min(100, $discount)) / 100));

        return round($discounted, 2);
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }

    protected function hydrateAutocompleteLabels(): void
    {
        $this->syncAccountSearchLabel();
        $this->syncContactSearchLabel();
        $this->syncOwnerSearchLabel();
        $this->syncPipelineSearchLabel();
        $this->syncStageSearchLabel();
    }

    protected function syncAccountSearchLabel(): void
    {
        $this->accountSearch = '';

        if ($this->accountId === '') {
            return;
        }

        $account = Account::query()
            ->select(['id', 'name'])
            ->find($this->accountId);

        $this->accountSearch = (string) ($account?->name ?? '');
    }

    protected function syncContactSearchLabel(): void
    {
        $this->contactSearch = '';

        if ($this->contactId === '') {
            return;
        }

        $contact = Contact::query()
            ->select(['id', 'first_name', 'last_name'])
            ->find($this->contactId);

        $this->contactSearch = (string) ($contact?->full_name ?? '');
    }

    protected function syncOwnerSearchLabel(): void
    {
        $this->ownerSearch = '';

        if ($this->ownerId === '') {
            return;
        }

        $owner = User::query()
            ->select(['id', 'full_name'])
            ->find($this->ownerId);

        $this->ownerSearch = (string) ($owner?->full_name ?? '');
    }

    protected function syncPipelineSearchLabel(): void
    {
        $this->pipelineSearch = '';

        if ($this->pipelineId === '') {
            return;
        }

        $pipeline = Pipeline::query()
            ->select(['id', 'name'])
            ->find($this->pipelineId);

        $this->pipelineSearch = (string) ($pipeline?->name ?? '');
    }

    protected function syncStageSearchLabel(): void
    {
        $this->stageSearch = '';

        if ($this->stageId === '') {
            return;
        }

        $stage = PipelineStage::query()
            ->select(['id', 'name', 'probability'])
            ->find($this->stageId);

        if (! $stage) {
            return;
        }

        $this->stageSearch = $this->formatStageLabel($stage);
    }

    protected function formatStageLabel(PipelineStage $stage): string
    {
        return $stage->name.' ('.$stage->probability.'%)';
    }

    protected function syncLineItemProductFromName(int $index, string $productName): void
    {
        if (! array_key_exists($index, $this->lineItems)) {
            return;
        }

        $productName = trim($productName);

        if ($productName === '') {
            $this->lineItems[$index]['product_id'] = '';

            return;
        }

        $product = Product::query()
            ->select(['id', 'name', 'unit_price'])
            ->active()
            ->where('name', $productName)
            ->first();

        if (! $product) {
            $this->lineItems[$index]['product_id'] = '';

            return;
        }

        $this->lineItems[$index]['product_id'] = (string) $product->id;
        $this->lineItems[$index]['product_name'] = (string) $product->name;
        $this->lineItems[$index]['unit_price'] = (float) $product->unit_price;
    }
}
