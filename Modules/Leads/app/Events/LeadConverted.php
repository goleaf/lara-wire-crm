<?php

namespace Modules\Leads\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Leads\Models\Lead;

class LeadConverted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Lead $lead
    ) {}
}
