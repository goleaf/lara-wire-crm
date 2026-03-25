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
        $isRead = fake()->boolean(40);
        $userId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'user_id' => (string) $userId,
            'type' => fake()->randomElement(['Reminder', 'Mention', 'Assignment', 'SLA Breach', 'Deal Update', 'Task Due', 'Case Update', 'Payment Recorded', 'Quote Accepted', 'Other']),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'is_read' => $isRead,
            'read_at' => $isRead ? now()->subMinutes(fake()->numberBetween(1, 600)) : null,
            'related_to_type' => User::class,
            'related_to_id' => (string) $userId,
            'action_url' => '/dashboard',
        ];
    }
}
