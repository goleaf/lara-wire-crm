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
        return [
            'case_id' => SupportCase::query()->value('id'),
            'user_id' => User::query()->value('id'),
            'body' => fake()->sentence(),
            'is_internal' => fake()->boolean(25),
        ];
    }
}
