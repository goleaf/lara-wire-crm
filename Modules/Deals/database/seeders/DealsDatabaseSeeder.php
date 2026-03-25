<?php

namespace Modules\Deals\Database\Seeders;

use Illuminate\Database\Seeder;

class DealsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PipelineSeeder::class,
            PipelineStageSeeder::class,
            DealSeeder::class,
        ]);
    }
}
