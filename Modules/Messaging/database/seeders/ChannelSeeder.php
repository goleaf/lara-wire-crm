<?php

namespace Modules\Messaging\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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

        $channel = Channel::query()->firstOrCreate([
            'name' => 'General',
            'type' => 'Public',
        ], [
            'created_by' => (string) $users->first()->id,
        ]);

        $channel->members()->syncWithoutDetaching($users->pluck('id')->all());
    }
}
