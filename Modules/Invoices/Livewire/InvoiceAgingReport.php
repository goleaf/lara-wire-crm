<?php

namespace Modules\Invoices\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Invoices\Models\Invoice;

class InvoiceAgingReport extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('invoices.view'), 403);
    }

    public function render(): View
    {
        $invoices = Invoice::query()
            ->select(['id', 'number', 'account_id', 'owner_id', 'due_date', 'total', 'amount_paid', 'status', 'currency'])
            ->with([
                'account:id,name',
                'owner:id,full_name',
            ])
            ->whereNotIn('status', ['Paid', 'Cancelled'])
            ->orderBy('due_date')
            ->get();

        $bucketed = [
            'current' => [],
            '1_30' => [],
            '31_60' => [],
            '61_90' => [],
            '90_plus' => [],
        ];

        foreach ($invoices as $invoice) {
            $daysOverdue = $invoice->due_date?->diffInDays(now(), false) ?? 0;

            if ($daysOverdue <= 0) {
                $bucketed['current'][] = $invoice;

                continue;
            }

            if ($daysOverdue <= 30) {
                $bucketed['1_30'][] = $invoice;

                continue;
            }

            if ($daysOverdue <= 60) {
                $bucketed['31_60'][] = $invoice;

                continue;
            }

            if ($daysOverdue <= 90) {
                $bucketed['61_90'][] = $invoice;

                continue;
            }

            $bucketed['90_plus'][] = $invoice;
        }

        $summary = collect($bucketed)->map(function (array $items): array {
            $collection = collect($items);

            return [
                'count' => $collection->count(),
                'value' => round((float) $collection->sum(fn ($invoice): float => $invoice->balance_due), 2),
            ];
        });

        return view('invoices::livewire.invoice-aging-report', [
            'bucketed' => $bucketed,
            'summary' => $summary,
        ])->extends('core::layouts.module', ['title' => 'Invoice Aging Report']);
    }
}
