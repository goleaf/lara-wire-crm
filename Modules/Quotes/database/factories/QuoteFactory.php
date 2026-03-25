<?php

namespace Modules\Quotes\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Quotes\Models\Quote;

class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired']);
        $sentAt = in_array($status, ['Sent', 'Accepted', 'Rejected', 'Expired'], true)
            ? now()->subDays($this->faker->numberBetween(1, 15))
            : null;
        $signedAt = $status === 'Accepted'
            ? now()->subDays($this->faker->numberBetween(1, 10))
            : null;
        $accountId = Account::query()->value('id') ?? Account::factory()->create()->getKey();
        $contactId = Contact::query()->value('id');
        $dealId = Deal::query()->value('id');
        $ownerId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'number' => 'QUO-'.now()->format('Y').'-'.$this->faker->unique()->numerify('####'),
            'name' => $this->faker->sentence(3),
            'deal_id' => $dealId,
            'contact_id' => $contactId,
            'account_id' => (string) $accountId,
            'owner_id' => (string) $ownerId,
            'status' => $status,
            'valid_until' => $this->faker->dateTimeBetween('-5 days', '+20 days'),
            'notes' => $this->faker->sentence(),
            'internal_notes' => $this->faker->sentence(),
            'subtotal' => 0,
            'discount_type' => 'Percentage',
            'discount_value' => 0,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'currency' => config('crm.default_currency.code', 'USD'),
            'signed_at' => $signedAt,
            'sent_at' => $sentAt,
            'pdf_path' => 'crm-pdfs/quotes/'.$this->faker->uuid().'.pdf',
        ];
    }
}
