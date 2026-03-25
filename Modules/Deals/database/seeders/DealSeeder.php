<?php

namespace Modules\Deals\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->select(['id'])->first();
        $pipeline = Pipeline::query()->where('is_default', true)->first();
        $stageIds = PipelineStage::query()
            ->where('pipeline_id', $pipeline?->id)
            ->pluck('id')
            ->all();
        $accountIds = Account::query()->pluck('id')->all();
        $contactIds = Contact::query()->pluck('id')->all();

        if (! $owner || ! $pipeline || $stageIds === [] || $accountIds === [] || $contactIds === []) {
            return;
        }

        $deals = Deal::factory()
            ->count(15)
            ->state(fn (): array => [
                'owner_id' => $owner->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => fake()->randomElement($stageIds),
                'account_id' => fake()->randomElement($accountIds),
                'contact_id' => fake()->randomElement($contactIds),
                'close_date' => now()->addDays(random_int(7, 120))->toDateString(),
                'source' => fake()->randomElement(['Referral', 'Event', 'Inbound']),
            ])
            ->create();

        $stagesById = PipelineStage::query()
            ->select(['id', 'name'])
            ->whereIn('id', $stageIds)
            ->get()
            ->keyBy('id');

        $deals->each(function (Deal $deal) use ($stagesById): void {
            $stageName = (string) ($stagesById[$deal->stage_id]->name ?? '');

            if ($stageName === 'Closed Lost') {
                $deal->forceFill([
                    'lost_reason' => fake()->randomElement(['Price', 'Competitor', 'No Budget', 'No Decision', 'Other']),
                    'lost_notes' => fake()->sentence(),
                    'closed_at' => now()->subDays(random_int(1, 20)),
                ])->saveQuietly();

                return;
            }

            if ($stageName === 'Closed Won') {
                $deal->forceFill([
                    'lost_reason' => null,
                    'lost_notes' => null,
                    'closed_at' => now()->subDays(random_int(1, 20)),
                ])->saveQuietly();

                return;
            }

            $deal->forceFill([
                'lost_reason' => null,
                'lost_notes' => null,
                'closed_at' => null,
            ])->saveQuietly();
        });
    }
}
