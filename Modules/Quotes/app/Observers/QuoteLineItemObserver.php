<?php

namespace Modules\Quotes\Observers;

use Modules\Quotes\Models\QuoteLineItem;

class QuoteLineItemObserver
{
    public function saved(QuoteLineItem $lineItem): void
    {
        $lineItem->quote?->recalculate();
    }

    public function deleted(QuoteLineItem $lineItem): void
    {
        $lineItem->quote?->recalculate();
    }
}
