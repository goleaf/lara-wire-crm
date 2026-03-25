<?php

namespace Modules\Calendar\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Calendar\Models\CalendarEvent;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::query()->select(['id'])->limit(8)->get();

        if ($owners->isEmpty()) {
            return;
        }

        $contacts = class_exists(Contact::class)
            ? Contact::query()->select(['id'])->limit(20)->get()
            : collect();

        $deals = class_exists(Deal::class)
            ? Deal::query()->select(['id'])->limit(20)->get()
            : collect();

        if ($contacts->isEmpty() || $deals->isEmpty()) {
            return;
        }

        $events = CalendarEvent::factory()
            ->count(20)
            ->state(function () use ($contacts, $deals, $owners): array {
                $start = now()->addHours(random_int(6, 420));
                $duration = random_int(30, 180);

                return [
                    'organizer_id' => (string) $owners->random()->id,
                    'title' => fake()->sentence(3),
                    'type' => fake()->randomElement(['Meeting', 'Demo', 'Follow-up', 'Reminder', 'Other']),
                    'start_at' => $start,
                    'end_at' => $start->copy()->addMinutes($duration),
                    'all_day' => false,
                    'location' => fake()->streetAddress(),
                    'description' => fake()->paragraph(),
                    'contact_id' => (string) $contacts->random()->id,
                    'deal_id' => (string) $deals->random()->id,
                    'reminder_minutes' => fake()->randomElement([5, 15, 30, 60, 1440]),
                    'recurrence' => fake()->randomElement(['None', 'Daily', 'Weekly', 'Monthly']),
                    'recurrence_end_date' => now()->addMonths(2)->toDateString(),
                    'status' => fake()->randomElement(['Scheduled', 'Completed', 'Cancelled']),
                    'color' => fake()->randomElement(['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b']),
                ];
            })
            ->create();

        $events->each(function (CalendarEvent $event) use ($owners): void {
            $attendeeIds = $owners
                ->pluck('id')
                ->reject(fn (string $id): bool => $id === $event->organizer_id)
                ->shuffle()
                ->take(random_int(1, min(3, max(1, $owners->count() - 1))))
                ->values()
                ->all();

            $event->attendees()->sync($attendeeIds);
        });
    }
}
