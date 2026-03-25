<?php

namespace Modules\Messaging\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $channelId = Channel::query()->value('id') ?? Channel::factory()->create()->getKey();
        $senderId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'channel_id' => (string) $channelId,
            'sender_id' => (string) $senderId,
            'body' => $this->faker->sentence(),
            'sent_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'edited_at' => $this->faker->dateTimeBetween('-6 days', 'now'),
            'is_deleted' => false,
            'parent_message_id' => null,
        ];
    }
}
