<?php

namespace Modules\Leads\Observers;

use Modules\Leads\Events\LeadConverted;
use Modules\Leads\Models\Lead;
use Modules\Leads\Services\LeadScoringService;

class LeadObserver
{
    public function __construct(
        protected LeadScoringService $scoringService
    ) {}

    public function creating(Lead $lead): void
    {
        $lead->score = $this->scoringService->calculateScore($lead);
    }

    public function updating(Lead $lead): void
    {
        if ($lead->isDirty(['email', 'phone', 'company', 'lead_source', 'status', 'campaign_id'])) {
            $lead->score = $this->scoringService->calculateScore($lead);
        }
    }

    public function updated(Lead $lead): void
    {
        if ($lead->wasChanged('status') && $lead->status === 'Converted') {
            event(new LeadConverted($lead));
        }
    }
}
