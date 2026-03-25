<?php

namespace Modules\Notifications\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Notifications\Models\CrmNotification;
use Modules\Users\Models\Team;

class NotificationService
{
    public function send(
        User|Collection $users,
        string $type,
        string $title,
        ?string $body = null,
        ?string $relatedType = null,
        ?string $relatedId = null,
        ?string $actionUrl = null,
    ): void {
        $targets = $users instanceof User ? collect([$users]) : $users;

        $targets
            ->filter(fn ($user): bool => $user instanceof User)
            ->unique('id')
            ->each(function (User $user) use ($type, $title, $body, $relatedType, $relatedId, $actionUrl): void {
                if (! $this->userWantsType($user, $type)) {
                    return;
                }

                CrmNotification::notify($user, $type, $title, [
                    'body' => $body,
                    'related_to_type' => $relatedType,
                    'related_to_id' => $relatedId,
                    'action_url' => $actionUrl,
                ]);
            });
    }

    public function notifyOwner(Model $record, string $type, string $message): void
    {
        $owner = $record->owner ?? null;

        if (! $owner && $record->getAttribute('owner_id')) {
            $owner = User::query()->find($record->getAttribute('owner_id'));
        }

        if (! $owner instanceof User) {
            return;
        }

        $this->send(
            $owner,
            $type,
            $message,
            $message,
            $record::class,
            (string) $record->getKey()
        );
    }

    public function notifyTeam(Team $team, string $type, string $message): void
    {
        $members = $team->members()
            ->select(['users.id', 'users.full_name', 'users.email', 'users.password'])
            ->get();

        $this->send($members, $type, $message, $message, Team::class, (string) $team->getKey());
    }

    public function markAllRead(User $user): void
    {
        CrmNotification::query()
            ->forUser((string) $user->getKey())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getUnreadCount(User $user): int
    {
        return CrmNotification::query()
            ->forUser((string) $user->getKey())
            ->unread()
            ->count();
    }

    protected function userWantsType(User $user, string $type): bool
    {
        $preferences = $user->user_notification_preferences;

        if (is_string($preferences)) {
            $preferences = json_decode($preferences, true);
        }

        if (! is_array($preferences)) {
            return true;
        }

        $types = $preferences['types'] ?? null;

        if (! is_array($types)) {
            return ! ($type === 'Reminder' && $this->isWithinQuietHours($preferences));
        }

        if (! (bool) ($types[$type] ?? true)) {
            return false;
        }

        if ($type === 'Reminder' && $this->isWithinQuietHours($preferences)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    protected function isWithinQuietHours(array $preferences): bool
    {
        $start = $preferences['quiet_hours_start'] ?? null;
        $end = $preferences['quiet_hours_end'] ?? null;

        if (! is_string($start) || ! is_string($end) || $start === '' || $end === '') {
            return false;
        }

        $now = now()->format('H:i');

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        }

        return $now >= $start || $now <= $end;
    }
}
