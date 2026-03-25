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

        Account::factory()
            ->count(10)
            ->state(fn (): array => ['owner_id' => $owner->id])
            ->create();
    }
}
