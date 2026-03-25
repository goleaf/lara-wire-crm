<?php

namespace Modules\Messaging\Observers;

use Illuminate\Support\Collection;
use Modules\Messaging\Models\Message;
use Modules\Messaging\Services\MessagingService;
use Modules\Notifications\Services\NotificationService;

class MessageObserver
{
    public function creating(Message $message): void
    {
        if (blank($message->sent_at)) {
            $message->sent_at = now();
        }
    }

    public function created(Message $message): void
    {
        $channel = $message->channel()
            ->select(['id', 'name', 'type', 'related_to_type', 'related_to_id', 'created_by'])
            ->with('members:id,full_name,email,password')
            ->first();

        if (! $channel) {
            return;
        }

        $mentionIds = app(MessagingService::class)->parseMentions((string) $message->body);

        if ($mentionIds !== []) {
            $message->mentions()->syncWithoutDetaching($mentionIds);
        }

        $channel->touch();

        if (! class_exists(NotificationService::class)) {
            return;
        }

        $notificationService = app(NotificationService::class);

        $recipients = $channel->members
            ->where('id', '!=', $message->sender_id)
            ->values();

        if ($recipients->isNotEmpty()) {
            $notificationService->send(
                $recipients,
                'Other',
                'New message in '.$channel->display_name,
                str((string) $message->body)->limit(140)->toString(),
                Message::class,
                (string) $message->getKey(),
                route('messages.show', ['channelId' => $channel->getKey()])
            );
        }

        if ($mentionIds !== []) {
            $mentionUsers = $channel->members
                ->whereIn('id', $mentionIds)
                ->where('id', '!=', $message->sender_id)
                ->values();

            if ($mentionUsers instanceof Collection && $mentionUsers->isNotEmpty()) {
                $notificationService->send(
                    $mentionUsers,
                    'Mention',
                    'You were mentioned in '.$channel->display_name,
                    str((string) $message->body)->limit(140)->toString(),
                    Message::class,
                    (string) $message->getKey(),
                    route('messages.show', ['channelId' => $channel->getKey()])
                );
            }
        }
    }
}
