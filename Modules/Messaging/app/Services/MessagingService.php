<?php

namespace Modules\Messaging\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\Messaging\Events\MessageSent;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;

class MessagingService
{
    /**
     * @param  array<int, string>  $fileIds
     */
    public function sendMessage(
        Channel $channel,
        User $sender,
        string $body,
        array $fileIds = [],
        ?string $parentMessageId = null,
    ): Message {
        $trimmedBody = trim($body);

        $channel->members()->syncWithoutDetaching([
            (string) $sender->getKey() => [
                'last_read_at' => now(),
            ],
        ]);

        $message = Message::query()->create([
            'channel_id' => (string) $channel->getKey(),
            'sender_id' => (string) $sender->getKey(),
            'body' => $trimmedBody,
            'sent_at' => now(),
            'is_deleted' => false,
            'parent_message_id' => $parentMessageId,
        ]);

        if ($fileIds !== []) {
            $message->attachments()->sync($fileIds);
        }

        MessageSent::dispatch($message->fresh(['sender:id,full_name,email', 'attachments:id,name,storage_path,disk']));

        return $message;
    }

    /**
     * @return array<int, string>
     */
    public function parseMentions(string $body): array
    {
        preg_match_all('/@([\p{L}\p{N}\._-]+)/u', $body, $matches);

        $tokens = collect($matches[1] ?? [])
            ->map(fn ($token) => trim((string) $token))
            ->filter()
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return [];
        }

        $users = User::query()
            ->select(['id', 'full_name', 'email'])
            ->where(function (Builder $query) use ($tokens): void {
                foreach ($tokens as $token) {
                    $query
                        ->orWhere('full_name', 'like', '%'.$token.'%')
                        ->orWhere('email', 'like', $token.'%');
                }
            })
            ->get();

        return $users
            ->filter(function (User $user) use ($tokens): bool {
                $name = mb_strtolower((string) $user->full_name);
                $email = mb_strtolower((string) $user->email);

                return $tokens->contains(function (string $token) use ($name, $email): bool {
                    $needle = mb_strtolower($token);

                    return str_contains($name, $needle) || str_starts_with($email, $needle);
                });
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    public function createDirectChannel(User $a, User $b): Channel
    {
        return Channel::findOrCreateDm($a, $b);
    }

    /**
     * @param  array<int, string>  $userIds
     */
    public function createGroupChannel(
        string $name,
        array $userIds,
        User $creator,
        string $type = 'Private',
        ?string $relatedType = null,
        ?string $relatedId = null,
    ): Channel {
        $channel = Channel::query()->create([
            'name' => trim($name),
            'type' => in_array($type, ['Public', 'Private'], true) ? $type : 'Private',
            'related_to_type' => $relatedType,
            'related_to_id' => $relatedId,
            'created_by' => (string) $creator->getKey(),
        ]);

        $memberIds = collect($userIds)
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->push((string) $creator->getKey())
            ->unique()
            ->values()
            ->all();

        $channel->members()->sync($memberIds);
        $channel->markAsRead($creator);

        return $channel->fresh();
    }
}
