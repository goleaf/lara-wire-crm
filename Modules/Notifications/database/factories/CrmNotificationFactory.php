<?php

namespace Modules\Notifications\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Notifications\Models\CrmNotification;

class CrmNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CrmNotification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['Reminder', 'Mention', 'Assignment', 'Deal Update', 'Other']),
            'title' => fake()->sentence(4),
            'body' => fake()->optional()->sentence(),
            'is_read' => fake()->boolean(40),
            'read_at' => null,
            'related_to_type' => null,
            'related_to_id' => null,
            'action_url' => fake()->optional()->url(),
        ];
    }
}
