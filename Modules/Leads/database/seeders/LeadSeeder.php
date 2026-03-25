<?php

namespace Modules\Leads\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Leads\Models\Lead;

class LeadSeeder extends Seeder
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

        Lead::factory()
            ->count(20)
            ->state(fn (): array => ['owner_id' => $owner->id])
            ->create();
    }
}
