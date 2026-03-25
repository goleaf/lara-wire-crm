<?php

namespace Modules\Notifications\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Notifications\Models\CrmNotification;
use Modules\Notifications\Services\NotificationService;

#[Defer]
class NotificationDropdown extends Component
{
    public function markRead(string $id): void
    {
        $notification = CrmNotification::query()
            ->forUser((string) auth()->id())
            ->findOrFail($id);

        $notification->markRead();

        $this->dispatch('notifications-refresh');

        if ($notification->action_url) {
            $this->redirect($notification->action_url, navigate: true);
        }
    }

    public function markAllRead(NotificationService $notificationService): void
    {
        $notificationService->markAllRead(auth()->user());
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
                'action_url',
                'created_at',
            ])
            ->forUser((string) auth()->id())
            ->latest()
            ->limit(8)
            ->get();

        return view('notifications::livewire.notification-dropdown', [
            'notifications' => $notifications,
        ]);
    }
}
