<?php

namespace Modules\Deals\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;

class DealFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Deal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $pipeline = Pipeline::query()->first() ?? Pipeline::factory()->create();
        $stage = PipelineStage::query()
            ->where('pipeline_id', $pipeline->id)
            ->orderBy('order')
            ->first() ?? PipelineStage::factory()->create([
                'pipeline_id' => $pipeline->id,
                'order' => 1,
            ]);

        return [
            'name' => fake()->catchPhrase(),
            'account_id' => Account::factory(),
            'contact_id' => Contact::factory(),
            'owner_id' => User::factory(),
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'amount' => fake()->randomFloat(2, 1000, 120000),
            'currency' => config('crm.default_currency.code', 'USD'),
            'probability' => $stage->probability,
            'expected_revenue' => 0,
            'close_date' => fake()->date(),
            'deal_type' => fake()->randomElement(['New Business', 'Renewal', 'Upsell', 'Cross-sell']),
            'lost_reason' => null,
            'lost_notes' => null,
            'source' => fake()->randomElement(['Referral', 'Event', 'Inbound']),
            'closed_at' => null,
        ];
    }
}
