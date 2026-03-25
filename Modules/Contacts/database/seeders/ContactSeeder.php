<?php

namespace Modules\Contacts\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->select(['id'])->first();
        $accountIds = Account::query()->select(['id'])->pluck('id')->all();

        if (! $owner || $accountIds === []) {
            return;
        }

        Contact::factory()
            ->count(25)
            ->state(fn (): array => [
                'owner_id' => $owner->id,
                'account_id' => fake()->randomElement($accountIds),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'mobile' => fake()->phoneNumber(),
                'job_title' => fake()->jobTitle(),
                'department' => fake()->word(),
                'birthday' => fake()->date(),
                'notes' => fake()->paragraph(),
            ])
            ->create();
    }
}
