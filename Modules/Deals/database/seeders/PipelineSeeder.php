<?php

namespace Modules\Deals\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Deals\Models\Pipeline;

class PipelineSeeder extends Seeder
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

        Pipeline::query()->firstOrCreate(
            ['name' => 'Default Sales Pipeline'],
            [
                'is_default' => true,
                'owner_id' => $owner->id,
            ],
        );
    }
}
