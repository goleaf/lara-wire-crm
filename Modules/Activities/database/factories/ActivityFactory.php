<?php

namespace Modules\Activities\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Activities\Models\Activity;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['Planned', 'Planned', 'Completed', 'Cancelled']);
        $dueDate = fake()->optional()->dateTimeBetween('-5 days', '+10 days');

        return [
            'type' => fake()->randomElement(['Meeting', 'Task', 'Note', 'SMS']),
            'subject' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'status' => $status,
            'priority' => fake()->randomElement(['Low', 'Normal', 'High']),
            'due_date' => $dueDate,
            'duration_minutes' => fake()->optional()->randomElement([15, 30, 60, 120]),
            'outcome' => $status === 'Completed' ? fake()->sentence() : null,
            'related_to_type' => null,
            'related_to_id' => null,
            'owner_id' => User::factory(),
            'reminder_at' => $dueDate && fake()->boolean(30) ? Carbon::instance($dueDate)->subMinutes(30) : null,
            'reminder_sent' => false,
            'completed_at' => $status === 'Completed' ? now()->subMinutes(fake()->numberBetween(5, 600)) : null,
        ];
    }
}
