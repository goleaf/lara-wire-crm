<?php

namespace Modules\Contacts\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\StreamedResponse;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Contacts\Models\Account;

class AccountIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $ownerFilter = '';

    public string $industryFilter = '';

    /**
     * @var array<int, string>
     */
    public array $selected = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('contacts.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingIndustryFilter(): void
    {
        $this->resetPage();
    }

    public function toggleSelection(string $id): void
    {
        if (in_array($id, $this->selected, true)) {
            $this->selected = array_values(array_filter(
                $this->selected,
                fn (string $selectedId): bool => $selectedId !== $id
            ));

            return;
        }

        $this->selected[] = $id;
    }

    public function deleteAccount(string $id): void
    {
        abort_unless(auth()->user()?->can('contacts.delete'), 403);

        Account::query()->whereKey($id)->delete();
        session()->flash('status', 'Account deleted.');
        $this->resetPage();
    }

    public function bulkDelete(): void
    {
        abort_unless(auth()->user()?->can('contacts.delete'), 403);

        if ($this->selected === []) {
            return;
        }

        Account::query()->whereIn('id', $this->selected)->delete();
        $this->selected = [];

        session()->flash('status', 'Selected accounts deleted.');
        $this->resetPage();
    }

    public function viewAccount(string $id): void
    {
        $this->redirectRoute('accounts.show', ['id' => $id], navigate: true);
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->can('contacts.export'), 403);

        $fileName = 'accounts-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, ['Name', 'Industry', 'Type', 'Phone', 'Owner', 'Contacts']);

            $this->filteredQuery()
                ->select(['id', 'name', 'industry', 'type', 'phone', 'owner_id'])
                ->with(['owner:id,full_name'])
                ->withCount('contacts')
                ->orderBy('name')
                ->chunk(250, function ($rows) use ($stream): void {
                    foreach ($rows as $row) {
                        fputcsv($stream, [
                            $row->name,
                            $row->industry,
                            $row->type,
                            $row->phone,
                            $row->owner?->full_name,
                            $row->contacts_count,
                        ]);
                    }
                });

            fclose($stream);
        }, $fileName);
    }

    public function render(): View
    {
        $accounts = $this->filteredQuery()
            ->select([
                'id',
                'name',
                'industry',
                'type',
                'phone',
                'owner_id',
            ])
            ->with(['owner:id,full_name'])
            ->withCount('contacts')
            ->orderBy('name')
            ->paginate(15);

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('contacts::livewire.account-index', [
            'accounts' => $accounts,
            'owners' => $owners,
            'industries' => ['Technology', 'Finance', 'Retail', 'Healthcare', 'Manufacturing', 'Education', 'Real Estate', 'Other'],
            'types' => ['Customer', 'Partner', 'Prospect', 'Vendor'],
        ])->extends('core::layouts.module', ['title' => 'Accounts']);
    }

    protected function filteredQuery(): Builder
    {
        return Account::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('industry', 'like', "%{$this->search}%")
                        ->orWhere('type', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->industryFilter !== '', fn ($query) => $query->where('industry', $this->industryFilter));
    }
}
