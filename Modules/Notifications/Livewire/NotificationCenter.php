<?php

namespace Modules\Notifications\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Notifications\Models\CrmNotification;

class NotificationCenter extends Component
{
    use WithPagination;

    public string $typeFilter = '';

    public string $readFilter = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    /**
     * @var array<int, string>
     */
    public array $selected = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('notifications.view'), 403);
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingReadFilter(): void
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

    public function toggleSelection(string $id): void
    {
        if (in_array($id, $this->selected, true)) {
            $this->selected = array_values(array_filter(
                $this->selected,
                fn (string $selected): bool => $selected !== $id
            ));

            return;
        }

        $this->selected[] = $id;
    }

    public function markSelectedRead(): void
    {
        abort_unless(auth()->user()?->can('notifications.edit'), 403);

        if ($this->selected === []) {
            return;
        }

        CrmNotification::query()
            ->forUser((string) auth()->id())
            ->whereIn('id', $this->selected)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->selected = [];
        $this->dispatch('notifications-refresh');
        session()->flash('status', 'Selected notifications marked as read.');
    }

    public function deleteSelected(): void
    {
        abort_unless(auth()->user()?->can('notifications.delete'), 403);

        if ($this->selected === []) {
            return;
        }

        CrmNotification::query()
            ->forUser((string) auth()->id())
            ->whereIn('id', $this->selected)
            ->delete();

        $this->selected = [];
        $this->dispatch('notifications-refresh');
        session()->flash('status', 'Selected notifications deleted.');
    }

    public function markRead(string $id): void
    {
        abort_unless(auth()->user()?->can('notifications.edit'), 403);

        $notification = CrmNotification::query()
            ->forUser((string) auth()->id())
            ->findOrFail($id);

        $notification->markRead();
        $this->dispatch('notifications-refresh');
    }

    public function render(): View
    {
        $notifications = CrmNotification::query()
            ->select([
                'id',
                'user_id',
                'type',
                'title',
                'body',
                'is_read',
                'read_at',
                'related_to_type',
                'related_to_id',
                'action_url',
                'created_at',
            ])
            ->forUser((string) auth()->id())
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->readFilter !== '', fn ($query) => $query->where('is_read', $this->readFilter === 'read'))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);

        return view('notifications::livewire.notification-center', [
            'notifications' => $notifications,
            'types' => [
                'Reminder',
                'Mention',
                'Assignment',
                'SLA Breach',
                'Deal Update',
                'Task Due',
                'Case Update',
                'Payment Recorded',
                'Quote Accepted',
                'Other',
            ],
        ])->extends('core::layouts.module', ['title' => 'Notifications']);
    }
}
