<?php

namespace Modules\Quotes\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Quotes\Models\Quote;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->select(['id'])->get();
        $accounts = Account::query()->select(['id'])->get();

        if ($users->isEmpty() || $accounts->isEmpty()) {
            return;
        }

        $deals = Deal::query()->select(['id'])->get();
        $contacts = Contact::query()->select(['id'])->get();
        $statuses = ['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'];

        for ($index = 1; $index <= 8; $index++) {
            Quote::query()->create([
                'name' => 'Quote '.$index,
                'deal_id' => $deals->random()->id ?? null,
                'contact_id' => $contacts->isNotEmpty() ? $contacts->random()->id : null,
                'account_id' => $accounts->random()->id,
                'owner_id' => $users->random()->id,
                'status' => $statuses[array_rand($statuses)],
                'valid_until' => now()->addDays(random_int(-5, 25))->toDateString(),
                'notes' => 'Standard terms apply.',
                'internal_notes' => 'Seeded quote.',
                'discount_type' => 'Percentage',
                'discount_value' => random_int(0, 15),
                'currency' => config('crm.default_currency.code', 'USD'),
            ]);
        }
    }
}
