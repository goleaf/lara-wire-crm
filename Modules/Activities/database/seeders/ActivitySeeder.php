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

        Activity::factory()
            ->count(24)
            ->make()
            ->each(function (Activity $activity) use ($owners, $relatedRecords): void {
                $activity->owner_id = (string) $owners->random()->id;

                if ($relatedRecords !== [] && fake()->boolean(75)) {
                    $related = $relatedRecords[array_rand($relatedRecords)];
                    $activity->related_to_type = $related['type'];
                    $activity->related_to_id = $related['id'];
                }

                $activity->save();
            });
    }
}
