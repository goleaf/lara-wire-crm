<?php

namespace Modules\Activities\Database\Seeders;

use Illuminate\Database\Seeder;

class ActivitiesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ActivitySeeder::class,
        ]);
    }
}
