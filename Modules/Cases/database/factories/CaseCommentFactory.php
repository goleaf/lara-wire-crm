<?php

namespace Modules\Cases\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cases\Models\CaseComment;
use Modules\Cases\Models\SupportCase;

class CaseCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CaseComment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $caseId = SupportCase::query()->value('id') ?? SupportCase::factory()->create()->getKey();
        $userId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'case_id' => (string) $caseId,
            'user_id' => (string) $userId,
            'body' => fake()->sentence(),
            'is_internal' => fake()->boolean(25),
        ];
    }
}
