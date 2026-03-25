<?php

namespace Modules\Reports\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Reports\Models\Report;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $ownerId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(10),
            'type' => fake()->randomElement(['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area']),
            'module' => fake()->randomElement(['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products']),
            'filters' => ['owner_scope' => 'all'],
            'group_by' => 'created_at',
            'metrics' => ['count', 'sum'],
            'date_field' => 'created_at',
            'date_range' => 'Custom',
            'custom_date_from' => now()->subDays(30)->toDateString(),
            'custom_date_to' => now()->toDateString(),
            'is_scheduled' => true,
            'schedule_frequency' => fake()->randomElement(['Daily', 'Weekly', 'Monthly']),
            'owner_id' => (string) $ownerId,
            'is_public' => false,
        ];
    }
}
