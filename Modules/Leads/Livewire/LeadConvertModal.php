<?php

namespace Modules\Leads\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Leads\Models\Lead;

#[Defer]
class LeadConvertModal extends Component
{
    public string $leadId;

    public int $step = 1;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $accountName = '';

    public string $dealName = '';

    public function mount(string $leadId): void
    {
        $this->leadId = $leadId;

        $lead = Lead::query()->select(['id', 'first_name', 'last_name', 'email', 'phone', 'company'])->findOrFail($leadId);

        $this->firstName = (string) $lead->first_name;
        $this->lastName = (string) $lead->last_name;
        $this->email = (string) ($lead->email ?? '');
        $this->phone = (string) ($lead->phone ?? '');
        $this->accountName = (string) ($lead->company ?: $lead->full_name.' Account');
        $this->dealName = $lead->full_name.' Opportunity';
    }

    public function nextStep(): void
    {
        $this->step = min(3, $this->step + 1);
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function confirm(): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        $lead = Lead::query()->findOrFail($this->leadId);
        $result = $lead->convert(auth()->user());

        session()->flash('status', 'Lead converted. Contact: '.$result['contact']->full_name);

        if ($result['deal']) {
            $this->redirectRoute('deals.show', ['id' => $result['deal']->id], navigate: true);

            return;
        }

        $this->redirectRoute('leads.show', ['id' => $lead->id], navigate: true);
    }

    public function render(): View
    {
        return view('leads::livewire.lead-convert-modal');
    }
}
