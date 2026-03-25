<?php

namespace Modules\Campaigns\Database\Seeders;

use Illuminate\Database\Seeder;

class CampaignsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CampaignSeeder::class,
        ]);
    }
}
