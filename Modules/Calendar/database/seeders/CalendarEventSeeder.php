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

        CalendarEvent::factory()
            ->count(20)
            ->make()
            ->each(function (CalendarEvent $event) use ($contacts, $deals, $owners): void {
                $event->organizer_id = (string) $owners->random()->id;
                $event->contact_id = $contacts->isNotEmpty() && fake()->boolean(40) ? (string) $contacts->random()->id : null;
                $event->deal_id = $deals->isNotEmpty() && fake()->boolean(40) ? (string) $deals->random()->id : null;
                $event->save();

                $attendeeIds = $owners
                    ->pluck('id')
                    ->reject(fn (string $id): bool => $id === $event->organizer_id)
                    ->shuffle()
                    ->take(fake()->numberBetween(0, 3))
                    ->values()
                    ->all();

                $event->attendees()->sync($attendeeIds);
            });
    }
}
