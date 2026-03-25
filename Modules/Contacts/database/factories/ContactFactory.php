<?php

namespace Modules\Contacts\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'mobile' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'department' => fake()->optional()->word(),
            'account_id' => Account::factory(),
            'owner_id' => User::factory(),
            'lead_source' => fake()->randomElement(['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other']),
            'do_not_contact' => fake()->boolean(15),
            'birthday' => fake()->optional()->date(),
            'preferred_channel' => fake()->randomElement(['Phone', 'SMS', 'In-person']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
