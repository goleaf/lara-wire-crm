<?php

namespace Modules\Messaging\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Messaging\Models\Channel;

class ChannelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Channel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $userId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'name' => $this->faker->words(2, true),
            'type' => $this->faker->randomElement(['Public', 'Private']),
            'related_to_type' => User::class,
            'related_to_id' => (string) $userId,
            'created_by' => (string) $userId,
        ];
    }
}
