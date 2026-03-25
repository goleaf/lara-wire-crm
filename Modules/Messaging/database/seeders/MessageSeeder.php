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

        Message::query()->firstOrCreate([
            'channel_id' => (string) $channel->id,
            'sender_id' => $senderId,
            'body' => 'Welcome to the team channel.',
            'sent_at' => now()->subDay(),
            'is_deleted' => false,
        ]);
    }
}
