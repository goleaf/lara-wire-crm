<?php

namespace Modules\Leads\Services;

use Modules\Leads\Models\Lead;

class LeadScoringService
{
    public function calculateScore(Lead $lead): int
    {
        $score = 0;

        if (filled($lead->email)) {
            $score += 20;
        }

        if (filled($lead->phone)) {
            $score += 15;
        }

        if (filled($lead->company)) {
            $score += 10;
        }

        if ($lead->lead_source === 'Referral') {
            $score += 20;
        }

        if ($lead->lead_source === 'Event') {
            $score += 15;
        }

        if ($lead->status === 'Contacted') {
            $score += 10;
        }

        if ($lead->status === 'Qualified') {
            $score += 25;
        }

        if (filled($lead->campaign_id)) {
            $score += 10;
        }

        return min(100, $score);
    }
}
