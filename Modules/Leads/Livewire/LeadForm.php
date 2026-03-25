<?php

namespace Modules\Leads\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;
use Modules\Leads\Models\Lead;
use Modules\Leads\Services\LeadScoringService;

class LeadForm extends Component
{
    public ?string $leadId = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $company = '';

    public string $email = '';

    public string $phone = '';

    public string $leadSource = 'Walk-in';

    public string $status = 'New';

    public int $score = 0;

    public string $rating = 'Cold';

    public string $campaignId = '';

    public string $ownerId = '';

    public bool $converted = false;

    public string $description = '';

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()?->can($id ? 'leads.edit' : 'leads.create'), 403);

        $this->ownerId = (string) auth()->id();

        if (! $id) {
            $this->recalculateScore();

            return;
        }

        $lead = Lead::query()->findOrFail($id);

        $this->leadId = $lead->id;
        $this->firstName = (string) $lead->first_name;
        $this->lastName = (string) $lead->last_name;
        $this->company = (string) ($lead->company ?? '');
        $this->email = (string) ($lead->email ?? '');
        $this->phone = (string) ($lead->phone ?? '');
        $this->leadSource = (string) $lead->lead_source;
        $this->status = (string) $lead->status;
        $this->score = (int) $lead->score;
        $this->rating = (string) $lead->rating;
        $this->campaignId = (string) ($lead->campaign_id ?? '');
        $this->ownerId = (string) $lead->owner_id;
        $this->converted = (bool) $lead->converted;
        $this->description = (string) ($lead->description ?? '');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['email', 'phone', 'company', 'leadSource', 'status', 'campaignId'], true)) {
            $this->recalculateScore();
        }
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());
        $this->recalculateScore();

        $lead = Lead::query()->updateOrCreate(
            ['id' => $this->leadId],
            [
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'company' => $this->nullableString($validated['company']),
                'email' => $this->nullableString($validated['email']),
                'phone' => $this->nullableString($validated['phone']),
                'lead_source' => $validated['leadSource'],
                'status' => $validated['status'],
                'score' => $this->score,
                'rating' => $validated['rating'],
                'campaign_id' => $this->nullableString($validated['campaignId']),
                'owner_id' => $validated['ownerId'],
                'converted' => $validated['converted'],
                'description' => $this->nullableString($validated['description']),
            ],
        );

        session()->flash('status', 'Lead saved successfully.');

        $this->redirectRoute('leads.show', ['id' => $lead->id], navigate: true);
    }

    public function setRating(string $value): void
    {
        if (! in_array($value, ['Hot', 'Warm', 'Cold'], true)) {
            return;
        }

        $this->rating = $value;
    }

    public function render(): View
    {
        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $campaigns = collect();

        if (class_exists(Campaign::class)) {
            $campaigns = Campaign::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        return view('leads::livewire.lead-form', [
            'campaigns' => $campaigns,
            'leadSources' => ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'],
            'owners' => $owners,
            'ratings' => ['Hot', 'Warm', 'Cold'],
            'statuses' => ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'],
        ])->extends('core::layouts.module', ['title' => $this->leadId ? 'Edit Lead' : 'New Lead']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $campaignRule = ['nullable'];

        if (class_exists(Campaign::class)) {
            $campaignRule[] = 'uuid';
            $campaignRule[] = 'exists:campaigns,id';
        }

        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'leadSource' => ['required', Rule::in(['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'])],
            'status' => ['required', Rule::in(['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'])],
            'rating' => ['required', Rule::in(['Hot', 'Warm', 'Cold'])],
            'campaignId' => $campaignRule,
            'ownerId' => ['required', 'uuid', 'exists:users,id'],
            'converted' => ['boolean'],
            'description' => ['nullable', 'string'],
        ];
    }

    protected function recalculateScore(): void
    {
        $lead = new Lead([
            'email' => $this->nullableString($this->email),
            'phone' => $this->nullableString($this->phone),
            'company' => $this->nullableString($this->company),
            'lead_source' => $this->leadSource,
            'status' => $this->status,
            'campaign_id' => $this->nullableString($this->campaignId),
        ]);

        $this->score = app(LeadScoringService::class)->calculateScore($lead);
    }

    protected function nullableString(?string $value): ?string
    {
        return filled($value) ? trim((string) $value) : null;
    }
}
