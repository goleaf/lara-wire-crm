<?php

namespace Modules\Campaigns\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Campaigns\Models\Campaign;
use Modules\Contacts\Models\Contact;
use Modules\Leads\Models\Lead;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->select(['id'])->get();

        if ($users->isEmpty()) {
            return;
        }

        $contacts = Contact::query()->select(['id'])->get();
        $leads = Lead::query()->select(['id'])->get();

        $types = ['Event', 'Cold Call', 'Direct Mail', 'In-person', 'Referral Program'];
        $statuses = ['Planned', 'Active', 'Completed', 'Paused', 'Active'];
        $contactStatuses = ['Targeted', 'Contacted', 'Responded', 'Converted', 'Opted Out'];

        for ($index = 0; $index < 5; $index++) {
            $campaign = Campaign::query()->create([
                'name' => 'Campaign '.($index + 1),
                'type' => $types[$index],
                'status' => $statuses[$index],
                'start_date' => now()->subDays(random_int(1, 20))->toDateString(),
                'end_date' => now()->addDays(random_int(10, 45))->toDateString(),
                'budget' => random_int(1000, 15000),
                'actual_cost' => random_int(500, 12000),
                'target_audience' => 'Segment '.chr(65 + $index),
                'expected_leads' => random_int(20, 140),
                'description' => 'Seeded campaign record.',
                'owner_id' => $users->random()->id,
            ]);

            if ($contacts->isNotEmpty()) {
                $contactSet = $contacts->shuffle()->take(min($contacts->count(), random_int(2, 6)));
                $payload = [];

                foreach ($contactSet as $contact) {
                    $payload[$contact->id] = [
                        'added_at' => now(),
                        'status' => $contactStatuses[array_rand($contactStatuses)],
                    ];
                }

                $campaign->contacts()->syncWithoutDetaching($payload);
            }

            if ($leads->isNotEmpty()) {
                $leadSet = $leads->shuffle()->take(min($leads->count(), random_int(2, 7)));

                foreach ($leadSet as $lead) {
                    $lead->forceFill(['campaign_id' => $campaign->id])->save();
                    $campaign->linkedLeads()->syncWithoutDetaching([
                        $lead->id => ['added_at' => now()],
                    ]);
                }
            }
        }
    }
}
