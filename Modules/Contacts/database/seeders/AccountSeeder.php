<?php

namespace Modules\Contacts\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->select(['id'])->first();

        if (! $owner) {
            return;
        }

        $accounts = Account::factory()
            ->count(10)
            ->state(fn (): array => [
                'owner_id' => $owner->id,
                'website' => fake()->url(),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'shipping_address' => [
                    'street' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->stateAbbr(),
                    'zip' => fake()->postcode(),
                    'country' => fake()->country(),
                ],
                'annual_revenue' => fake()->randomFloat(2, 50_000, 8_000_000),
                'employee_count' => fake()->numberBetween(5, 3500),
                'tags' => [fake()->word(), fake()->word()],
            ])
            ->create();

        $parentId = (string) $accounts->first()->id;

        $accounts
            ->slice(1, 4)
            ->each(fn (Account $account): bool => $account->update(['parent_account_id' => $parentId]));
    }
}
