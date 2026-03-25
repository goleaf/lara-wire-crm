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

        if (! $owner || ! $pipeline || $stageIds === [] || $accountIds === []) {
            return;
        }

        Deal::factory()
            ->count(15)
            ->state(fn (): array => [
                'owner_id' => $owner->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => fake()->randomElement($stageIds),
                'account_id' => fake()->randomElement($accountIds),
                'contact_id' => $contactIds !== [] ? fake()->randomElement($contactIds) : null,
            ])
            ->create();
    }
}
