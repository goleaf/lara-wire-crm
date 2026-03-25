<?php

namespace Modules\Campaigns\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;

class CampaignForm extends Component
{
    public ?string $campaignId = null;

    public string $name = '';

    public string $type = 'Other';

    public string $status = 'Planned';

    public string $start_date = '';

    public string $end_date = '';

    public string $budget = '0';

    public string $actual_cost = '0';

    public string $target_audience = '';

    public string $expected_leads = '0';

    public string $description = '';

    public string $owner_id = '';

    public function mount(?string $id = null): void
    {
        $this->campaignId = $id;
        $this->owner_id = (string) auth()->id();

        if ($this->campaignId !== null) {
            abort_unless(auth()->user()?->can('campaigns.edit'), 403);
            $this->loadCampaign();

            return;
        }

        abort_unless(auth()->user()?->can('campaigns.create'), 403);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program', 'Other'])],
            'status' => ['required', Rule::in(['Planned', 'Active', 'Completed', 'Paused'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['required', 'numeric', 'min:0'],
            'actual_cost' => ['required', 'numeric', 'min:0'],
            'target_audience' => ['nullable', 'string'],
            'expected_leads' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'owner_id' => ['required', Rule::exists('users', 'id')],
        ]);

        $campaign = Campaign::query()->updateOrCreate(
            ['id' => $this->campaignId],
            [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'status' => $validated['status'],
                'start_date' => $this->nullableString($validated['start_date']),
                'end_date' => $this->nullableString($validated['end_date']),
                'budget' => (float) $validated['budget'],
                'actual_cost' => (float) $validated['actual_cost'],
                'target_audience' => $this->nullableString($validated['target_audience']),
                'expected_leads' => (int) $validated['expected_leads'],
                'description' => $this->nullableString($validated['description']),
                'owner_id' => $validated['owner_id'],
            ]
        );

        session()->flash('status', 'Campaign saved.');
        $this->redirectRoute('campaigns.show', ['id' => $campaign->getKey()], navigate: true);
    }

    public function render(): View
    {
        return view('campaigns::livewire.campaign-form', [
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'types' => ['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program', 'Other'],
            'statuses' => ['Planned', 'Active', 'Completed', 'Paused'],
        ])->extends('core::layouts.module', [
            'title' => $this->campaignId ? 'Edit Campaign' : 'Create Campaign',
        ]);
    }

    protected function loadCampaign(): void
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
            ->findOrFail($this->campaignId);

        $this->name = (string) $campaign->name;
        $this->type = (string) $campaign->type;
        $this->status = (string) $campaign->status;
        $this->start_date = $campaign->start_date?->toDateString() ?? '';
        $this->end_date = $campaign->end_date?->toDateString() ?? '';
        $this->budget = (string) $campaign->budget;
        $this->actual_cost = (string) $campaign->actual_cost;
        $this->target_audience = (string) ($campaign->target_audience ?? '');
        $this->expected_leads = (string) $campaign->expected_leads;
        $this->description = (string) ($campaign->description ?? '');
        $this->owner_id = (string) $campaign->owner_id;
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
