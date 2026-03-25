<?php

namespace Modules\Messaging\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
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
        return [
            'channel_id' => Channel::query()->value('id') ?? Str::uuid()->toString(),
            'sender_id' => User::query()->value('id') ?? Str::uuid()->toString(),
            'body' => $this->faker->sentence(),
            'sent_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'edited_at' => null,
            'is_deleted' => false,
            'parent_message_id' => null,
        ];
    }
}
