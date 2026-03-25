<?php

namespace Modules\Leads\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Leads\Models\Lead;

class LeadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => fake()->optional()->company(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'lead_source' => fake()->randomElement(['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other']),
            'status' => fake()->randomElement(['New', 'Contacted', 'Qualified', 'Unqualified']),
            'score' => fake()->numberBetween(5, 90),
            'rating' => fake()->randomElement(['Hot', 'Warm', 'Cold']),
            'campaign_id' => null,
            'owner_id' => User::factory(),
            'converted' => false,
            'converted_to_contact_id' => null,
            'converted_to_deal_id' => null,
            'converted_at' => null,
            'description' => fake()->optional()->sentence(),
        ];
    }
}
