<?php

namespace Modules\Cases\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

class SupportCaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = SupportCase::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $ownerId = User::query()->value('id') ?? User::factory()->create()->getKey();
        $accountId = Account::query()->value('id') ?? Account::factory()->create()->getKey();
        $contactId = Contact::query()->value('id') ?? Contact::factory()->create()->getKey();
        $dealId = Deal::query()->value('id');

        return [
            'number' => sprintf('CASE-%04d', fake()->unique()->numberBetween(1, 9999)),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['Open', 'In Progress', 'Pending', 'Resolved', 'Closed']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'type' => fake()->randomElement(['Bug', 'Feature Request', 'Question', 'Complaint', 'Other']),
            'contact_id' => (string) $contactId,
            'account_id' => (string) $accountId,
            'deal_id' => $dealId,
            'owner_id' => (string) $ownerId,
            'channel' => fake()->randomElement(['Phone', 'In-person', 'Internal Portal', 'Other']),
            'resolution_notes' => fake()->sentence(),
        ];
    }
}
