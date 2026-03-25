<?php

namespace Modules\Messaging\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;
use Modules\Deals\Models\Deal;
use Modules\Messaging\Models\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->select(['id'])
            ->limit(5)
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $creatorId = (string) $users->first()->id;
        $dealsId = Deal::query()->value('id');
        $accountId = Account::query()->value('id');

        $channels = collect([
            [
                'name' => 'General',
                'type' => 'Public',
                'related_to_type' => Deal::class,
                'related_to_id' => $dealsId,
            ],
            [
                'name' => 'Finance Ops',
                'type' => 'Private',
                'related_to_type' => Account::class,
                'related_to_id' => $accountId,
            ],
        ]);

        $channels
            ->filter(fn (array $channel): bool => filled($channel['related_to_id']))
            ->each(function (array $channelData) use ($users, $creatorId): void {
                $channel = Channel::query()->updateOrCreate(
                    ['name' => $channelData['name'], 'type' => $channelData['type']],
                    [
                        'related_to_type' => $channelData['related_to_type'],
                        'related_to_id' => (string) $channelData['related_to_id'],
                        'created_by' => $creatorId,
                    ]
                );

                $memberPayload = $users
                    ->pluck('id')
                    ->mapWithKeys(fn (string $userId): array => [
                        $userId => [
                            'last_read_at' => now()->subMinutes(random_int(1, 180)),
                            'is_muted' => random_int(0, 5) === 0,
                        ],
                    ])
                    ->all();

                $channel->members()->syncWithoutDetaching($memberPayload);
            });
    }
}
