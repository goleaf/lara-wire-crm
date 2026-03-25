<?php

namespace Modules\Invoices\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Contacts\Models\Account;
use Modules\Invoices\Models\Invoice;

class InvoiceIndex extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $accountFilter = '';

    public string $overdueFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('invoices.view'), 403);
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOverdueFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function cancelInvoice(string $invoiceId): void
    {
        abort_unless(auth()->user()?->can('invoices.edit'), 403);

        Invoice::query()->whereKey($invoiceId)->update(['status' => 'Cancelled']);

        session()->flash('status', 'Invoice cancelled.');
    }

    public function deleteInvoice(string $invoiceId): void
    {
        abort_unless(auth()->user()?->can('invoices.delete'), 403);

        Invoice::query()->whereKey($invoiceId)->delete();

        session()->flash('status', 'Invoice deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $baseQuery = Invoice::query()
            ->select([
                'id',
                'number',
                'account_id',
                'deal_id',
                'status',
                'issue_date',
                'due_date',
                'total',
                'amount_paid',
                'owner_id',
                'currency',
            ])
            ->with([
                'account:id,name',
                'deal:id,name',
                'owner:id,full_name',
            ])
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->accountFilter !== '', fn ($query) => $query->where('account_id', $this->accountFilter))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('issue_date', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('issue_date', '<=', $this->dateTo))
            ->when($this->overdueFilter === '1', fn ($query) => $query->overdue())
            ->when($this->overdueFilter === '0', function ($query): void {
                $query->where(function ($inner): void {
                    $inner
                        ->whereDate('due_date', '>=', now()->toDateString())
                        ->orWhereIn('status', ['Paid', 'Cancelled']);
                });
            });

        $summaryRows = (clone $baseQuery)
            ->get(['id', 'total', 'amount_paid', 'status']);

        $summary = [
            'issued' => round((float) $summaryRows->sum('total'), 2),
            'paid' => round((float) $summaryRows->sum('amount_paid'), 2),
            'overdue' => round((float) $summaryRows->where('status', 'Overdue')->sum('total'), 2),
            'outstanding' => round((float) $summaryRows->sum(fn ($row): float => max(0, (float) $row->total - (float) $row->amount_paid)), 2),
        ];

        $invoices = $baseQuery
            ->orderByDesc('issue_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        $accounts = Account::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('invoices::livewire.invoice-index', [
            'accounts' => $accounts,
            'invoices' => $invoices,
            'owners' => User::query()->select(['id', 'full_name'])->orderBy('full_name')->get(),
            'statuses' => ['Draft', 'Issued', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled'],
            'summary' => $summary,
        ])->extends('core::layouts.module', ['title' => 'Invoices']);
    }
}
