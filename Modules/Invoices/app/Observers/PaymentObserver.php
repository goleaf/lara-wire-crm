<?php

namespace Modules\Invoices\Observers;

use Modules\Invoices\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }

    public function updated(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }

    public function deleted(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }
}
