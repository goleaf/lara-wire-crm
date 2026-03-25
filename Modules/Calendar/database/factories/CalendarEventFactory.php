<?php

namespace Modules\Calendar\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Calendar\Models\CalendarEvent;

class CalendarEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CalendarEvent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('-7 days', '+20 days');
        $type = fake()->randomElement(['Meeting', 'Demo', 'Follow-up', 'Reminder', 'Other']);

        return [
            'title' => fake()->sentence(3),
            'type' => $type,
            'start_at' => $startAt,
            'end_at' => fake()->boolean(75) ? Carbon::instance($startAt)->addMinutes(fake()->numberBetween(30, 180)) : null,
            'all_day' => fake()->boolean(15),
            'location' => fake()->streetAddress(),
            'description' => fake()->paragraph(),
            'organizer_id' => User::factory(),
            'contact_id' => null,
            'deal_id' => null,
            'reminder_minutes' => fake()->randomElement([5, 15, 30, 60, 1440]),
            'recurrence' => fake()->randomElement(['None', 'Daily', 'Weekly', 'Monthly']),
            'recurrence_end_date' => fake()->boolean(30) ? now()->addMonths(2)->toDateString() : null,
            'status' => fake()->randomElement(['Scheduled', 'Completed', 'Cancelled']),
            'color' => fake()->randomElement(['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b']),
        ];
    }
}
