<?php

namespace Modules\Cases\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Cases\Models\SupportCase;

class CaseCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->select(['id'])->get();
        $cases = SupportCase::query()->select(['id'])->get();

        if ($users->isEmpty() || $cases->isEmpty()) {
            return;
        }

        foreach ($cases as $supportCase) {
            $commentCount = random_int(1, 3);

            for ($index = 0; $index < $commentCount; $index++) {
                $supportCase->comments()->create([
                    'user_id' => $users->random()->id,
                    'body' => fake()->sentence(random_int(8, 16)),
                    'is_internal' => random_int(0, 4) === 0,
                ]);
            }
        }
    }
}
