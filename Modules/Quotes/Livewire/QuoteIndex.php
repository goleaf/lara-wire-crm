<?php

namespace Modules\Quotes\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Services\QuoteService;

class QuoteIndex extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $ownerFilter = '';

    public string $expiredFilter = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('quotes.view'), 403);
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingExpiredFilter(): void
    {
        $this->resetPage();
    }

    public function changeStatus(string $quoteId, string $status, QuoteService $quoteService): void
    {
        abort_unless(auth()->user()?->can('quotes.edit'), 403);

        $quote = Quote::query()->findOrFail($quoteId);

        match ($status) {
            'Sent' => $quoteService->markSent($quote),
            'Accepted' => $quoteService->markAccepted($quote),
            'Rejected' => $quoteService->markRejected($quote),
            default => $quote->update(['status' => $status]),
        };

        session()->flash('status', 'Quote status updated.');
    }

    public function delete(string $quoteId): void
    {
        abort_unless(auth()->user()?->can('quotes.delete'), 403);

        Quote::query()->whereKey($quoteId)->delete();
        session()->flash('status', 'Quote deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $quotes = Quote::query()
            ->select([
                'id',
                'number',
                'name',
                'account_id',
                'deal_id',
                'owner_id',
                'status',
                'total',
                'valid_until',
                'currency',
            ])
            ->with([
                'account:id,name',
                'deal:id,name',
                'owner:id,full_name',
            ])
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->expiredFilter === '1', fn ($query) => $query->expired())
            ->when($this->expiredFilter === '0', function ($query): void {
                $query->where(function ($inner): void {
                    $inner
                        ->whereNull('valid_until')
                        ->orWhereDate('valid_until', '>=', now()->toDateString())
                        ->orWhere('status', 'Accepted');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('quotes::livewire.quote-index', [
            'owners' => $owners,
            'quotes' => $quotes,
            'statuses' => ['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'],
        ])->extends('core::layouts.module', ['title' => 'Quotes']);
    }
}
