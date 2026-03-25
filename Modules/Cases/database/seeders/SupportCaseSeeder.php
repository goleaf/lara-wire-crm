<?php

namespace Modules\Cases\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

class SupportCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::query()->select(['id'])->get();

        if ($owners->isEmpty()) {
            return;
        }

        $accounts = Account::query()->select(['id'])->get();
        $contacts = Contact::query()->select(['id'])->get();
        $deals = Deal::query()->select(['id'])->get();

        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $statuses = ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'];
        $types = ['Bug', 'Feature Request', 'Question', 'Complaint', 'Other'];
        $channels = ['Phone', 'In-person', 'Internal Portal', 'Other'];

        for ($index = 1; $index <= 15; $index++) {
            $status = $statuses[array_rand($statuses)];

            $supportCase = SupportCase::query()->create([
                'title' => 'Support Case '.$index,
                'description' => 'Seeded support case details.',
                'status' => $status,
                'priority' => $priorities[array_rand($priorities)],
                'type' => $types[array_rand($types)],
                'contact_id' => $contacts->isNotEmpty() ? $contacts->random()->id : null,
                'account_id' => $accounts->isNotEmpty() ? $accounts->random()->id : null,
                'deal_id' => $deals->isNotEmpty() ? $deals->random()->id : null,
                'owner_id' => $owners->random()->id,
                'channel' => $channels[array_rand($channels)],
                'resolution_notes' => 'Resolution notes prepared for support workflow demo.',
            ]);

            if ($status === 'Resolved') {
                $supportCase->forceFill([
                    'first_response_at' => now()->subHours(random_int(20, 72)),
                    'resolved_at' => now()->subHours(random_int(1, 18)),
                    'satisfaction_score' => random_int(3, 5),
                ])->saveQuietly();
            }

            if ($status === 'Closed') {
                $supportCase->forceFill([
                    'first_response_at' => now()->subHours(random_int(24, 84)),
                    'resolved_at' => now()->subHours(random_int(12, 32)),
                    'closed_at' => now()->subHours(random_int(1, 11)),
                    'satisfaction_score' => random_int(3, 5),
                ])->saveQuietly();

                continue;
            }

            if (in_array($status, ['Open', 'In Progress', 'Pending'], true)) {
                $supportCase->forceFill([
                    'first_response_at' => now()->subHours(random_int(1, 48)),
                ])->saveQuietly();
            }
        }
    }
}
