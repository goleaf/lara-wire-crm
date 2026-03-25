<?php

namespace Modules\Quotes\Observers;

use Modules\Quotes\Models\Quote;

class QuoteObserver
{
    public function creating(Quote $quote): void
    {
        if (blank($quote->number)) {
            $quote->number = $quote->generateNumber();
        }
    }
}
