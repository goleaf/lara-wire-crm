<?php

namespace Modules\Notifications\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Defer;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Notifications\Services\NotificationService;

#[Defer]
class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public bool $open = false;

    public function mount(NotificationService $notificationService): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->unreadCount = $notificationService->getUnreadCount(auth()->user());
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    #[On('notifications-refresh')]
    public function refreshCount(NotificationService $notificationService): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->unreadCount = $notificationService->getUnreadCount(auth()->user());
    }

    public function render(): View
    {
        return view('notifications::livewire.notification-bell');
    }
}
