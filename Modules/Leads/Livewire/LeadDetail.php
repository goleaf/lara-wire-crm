<?php

namespace Modules\Leads\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class LeadDetail extends Component
{
    public Lead $lead;

    public string $tab = 'overview';

    public bool $showConvertModal = false;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('leads.view'), 403);

        $query = Lead::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'company',
                'email',
                'phone',
                'lead_source',
                'status',
                'score',
                'rating',
                'campaign_id',
                'owner_id',
                'converted',
                'converted_to_contact_id',
                'converted_to_deal_id',
                'converted_at',
                'description',
                'created_at',
            ])
            ->with([
                'owner:id,full_name',
                'convertedContact:id,first_name,last_name',
            ]);

        if (class_exists(Campaign::class)) {
            $query->with('campaign:id,name');
        }

        if (class_exists(Deal::class)) {
            $query->with('convertedDeal:id,name');
        }

        $this->lead = $query->findOrFail($id);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function openConvertModal(): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        if ($this->lead->converted) {
            return;
        }

        $this->showConvertModal = true;
    }

    public function closeConvertModal(): void
    {
        $this->showConvertModal = false;
    }

    public function render(): View
    {
        return view('leads::livewire.lead-detail', [
            'tabs' => ['overview', 'activities', 'notes', 'files'],
        ])->extends('core::layouts.module', ['title' => $this->lead->full_name]);
    }
}
