<?php

namespace Modules\Quotes\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Contacts\Models\Account;
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
        return [
            'number' => 'QUO-'.now()->format('Y').'-'.$this->faker->unique()->numerify('####'),
            'name' => $this->faker->sentence(3),
            'deal_id' => null,
            'contact_id' => null,
            'account_id' => Account::query()->value('id'),
            'owner_id' => User::query()->value('id') ?? Str::uuid()->toString(),
            'status' => $this->faker->randomElement(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired']),
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
            'signed_at' => null,
            'sent_at' => null,
            'pdf_path' => null,
        ];
    }
}
