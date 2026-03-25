<?php

namespace Modules\Campaigns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Campaigns\Models\Campaign;

class CampaignRoiCard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public function render(): View
    {
        $campaigns = Campaign::query()
            ->select(['id', 'name', 'budget', 'actual_cost', 'owner_id'])
            ->withCount('leads')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->sortByDesc(fn (Campaign $campaign): float => $campaign->roi)
            ->take(5)
            ->values();

        return view('campaigns::livewire.campaign-roi-card', [
            'campaigns' => $campaigns,
            'maxRoi' => max(1, (float) $campaigns->max(fn (Campaign $campaign): float => abs($campaign->roi))),
        ]);
    }
}
