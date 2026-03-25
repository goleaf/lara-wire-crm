<?php

namespace Modules\Activities\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Activities\Models\Activity;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::query()->select(['id'])->limit(5)->get();

        if ($owners->isEmpty()) {
            return;
        }

        /** @var array<int, array{type:?string,id:?string}> $relatedRecords */
        $relatedRecords = collect([
            class_exists(Account::class) ? Account::query()->select(['id'])->limit(4)->get()->map(fn ($record) => ['type' => Account::class, 'id' => (string) $record->id]) : collect(),
            class_exists(Contact::class) ? Contact::query()->select(['id'])->limit(4)->get()->map(fn ($record) => ['type' => Contact::class, 'id' => (string) $record->id]) : collect(),
            class_exists(Deal::class) ? Deal::query()->select(['id'])->limit(4)->get()->map(fn ($record) => ['type' => Deal::class, 'id' => (string) $record->id]) : collect(),
            class_exists(Lead::class) ? Lead::query()->select(['id'])->limit(4)->get()->map(fn ($record) => ['type' => Lead::class, 'id' => (string) $record->id]) : collect(),
        ])->flatten(1)->values()->all();

        if ($relatedRecords === []) {
            return;
        }

        $activities = Activity::factory()
            ->count(24)
            ->state(function () use ($owners, $relatedRecords): array {
                $related = $relatedRecords[array_rand($relatedRecords)];
                $dueDate = now()->addHours(random_int(2, 240));
                $status = fake()->randomElement(['Planned', 'Completed', 'Cancelled']);

                return [
                    'owner_id' => (string) $owners->random()->id,
                    'type' => fake()->randomElement(['Meeting', 'Task', 'Note', 'SMS']),
                    'subject' => fake()->sentence(4),
                    'description' => fake()->paragraph(),
                    'status' => $status,
                    'priority' => fake()->randomElement(['Low', 'Normal', 'High']),
                    'due_date' => $dueDate,
                    'duration_minutes' => fake()->randomElement([15, 30, 60, 120]),
                    'outcome' => $status === 'Completed' ? fake()->sentence() : 'Planned action pending.',
                    'related_to_type' => $related['type'],
                    'related_to_id' => $related['id'],
                    'reminder_at' => $dueDate->copy()->subMinutes(30),
                    'reminder_sent' => false,
                    'completed_at' => $status === 'Completed' ? now()->subMinutes(random_int(10, 500)) : null,
                ];
            })
            ->create();

        $activities->each(function (Activity $activity) use ($owners): void {
            $attendees = $owners
                ->pluck('id')
                ->reject(fn (string $id): bool => $id === $activity->owner_id)
                ->shuffle()
                ->take(random_int(1, min(3, max(1, $owners->count() - 1))))
                ->values()
                ->all();

            if ($attendees !== []) {
                $activity->attendees()->sync($attendees);
            }
        });
    }
}
