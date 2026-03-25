<?php

namespace Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Notifications\Models\CrmNotification;
use Modules\Notifications\Services\NotificationService;

class NotificationsController extends Controller
{
    public function markRead(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('notifications.edit'), 403);

        $notification = CrmNotification::query()
            ->forUser((string) auth()->id())
            ->findOrFail($id);

        $notification->markRead();

        return redirect($notification->action_url ?: route('notifications.index'));
    }

    public function markAllRead(NotificationService $notificationService): RedirectResponse
    {
        abort_unless(auth()->user()?->can('notifications.edit'), 403);

        $notificationService->markAllRead(auth()->user());

        return back()->with('status', 'All notifications marked as read.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('notifications.delete'), 403);

        CrmNotification::query()
            ->forUser((string) auth()->id())
            ->whereKey($id)
            ->delete();

        return back()->with('status', 'Notification deleted.');
    }
}
