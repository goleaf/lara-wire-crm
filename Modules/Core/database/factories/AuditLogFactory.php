<?php

namespace Modules\Core\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\AuditLog;

class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->value('id'),
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'model_type' => User::class,
            'model_id' => (string) (User::query()->value('id') ?? fake()->uuid()),
            'old_values' => ['status' => 'old'],
            'new_values' => ['status' => 'new'],
            'ip_address' => fake()->ipv4(),
            'created_at' => now()->subMinutes(fake()->numberBetween(1, 300)),
        ];
    }
}
