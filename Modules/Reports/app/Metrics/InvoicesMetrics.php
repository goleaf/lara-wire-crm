<?php

namespace Modules\Reports\Metrics;

use Illuminate\Support\Collection;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\Payment;

class InvoicesMetrics
{
    public function totalIssued(): float
    {
        return (float) Invoice::query()->sum('total');
    }

    public function totalPaid(): float
    {
        return (float) Invoice::query()->sum('amount_paid');
    }

    public function outstanding(): float
    {
        return (float) Invoice::query()->sum('total') - (float) Invoice::query()->sum('amount_paid');
    }

    public function overdueAmount(): float
    {
        return (float) Invoice::query()->overdue()->sum('total');
    }

    public function avgPaymentDays(): float
    {
        $payments = Payment::query()
            ->select(['id', 'invoice_id', 'paid_at'])
            ->with(['invoice:id,issue_date'])
            ->get();

        return round((float) $payments->avg(function (Payment $payment): float {
            $issueDate = $payment->invoice?->issue_date;

            if (! $issueDate || ! $payment->paid_at) {
                return 0.0;
            }

            return (float) $issueDate->diffInDays($payment->paid_at);
        }), 2);
    }

    /**
     * @return Collection<string, float>
     */
    public function revenueByMonth(): Collection
    {
        return Invoice::query()
            ->select(['id', 'total', 'issue_date'])
            ->get()
            ->groupBy(fn (Invoice $invoice): string => $invoice->issue_date?->format('Y-m') ?? 'Unknown')
            ->map(fn (Collection $items): float => (float) $items->sum('total'));
    }
}
