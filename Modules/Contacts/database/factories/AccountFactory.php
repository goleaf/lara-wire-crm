<?php

namespace Modules\Contacts\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Contacts\Models\Account;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement(['Technology', 'Finance', 'Retail', 'Healthcare', 'Manufacturing', 'Education', 'Real Estate', 'Other']),
            'type' => fake()->randomElement(['Customer', 'Partner', 'Prospect', 'Vendor']),
            'website' => fake()->optional()->url(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'billing_address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->stateAbbr(),
                'zip' => fake()->postcode(),
                'country' => fake()->country(),
            ],
            'shipping_address' => null,
            'annual_revenue' => fake()->optional()->randomFloat(2, 10000, 5000000),
            'employee_count' => fake()->optional()->numberBetween(2, 2500),
            'owner_id' => User::factory(),
            'parent_account_id' => null,
            'tags' => [],
        ];
    }
}
