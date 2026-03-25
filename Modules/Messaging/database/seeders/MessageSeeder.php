<?php

namespace Modules\Messaging\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channel = Channel::query()
            ->select(['id'])
            ->with('members:id')
            ->first();

        if (! $channel || $channel->members->isEmpty()) {
            return;
        }

        $senderId = (string) $channel->members->first()->id;
        $replySender = (string) $channel->members->skip(1)->first()?->id ?: $senderId;

        $parent = Message::query()->firstOrCreate([
            'channel_id' => (string) $channel->id,
            'sender_id' => $senderId,
            'body' => 'Welcome to the team channel.',
            'sent_at' => now()->subDay(),
            'is_deleted' => false,
        ]);

        $parent->forceFill([
            'edited_at' => now()->subHours(20),
            'parent_message_id' => null,
        ])->saveQuietly();

        Message::query()->updateOrCreate(
            [
                'channel_id' => (string) $channel->id,
                'sender_id' => $replySender,
                'parent_message_id' => (string) $parent->getKey(),
            ],
            [
                'body' => 'Thread reply for seeded conversation context.',
                'sent_at' => now()->subHours(22),
                'edited_at' => now()->subHours(21),
                'is_deleted' => false,
            ]
        );
    }
}
