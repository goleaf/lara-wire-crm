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
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(10),
            'type' => fake()->randomElement(['Table', 'Bar', 'Line', 'Funnel', 'Pie', 'KPI', 'Area']),
            'module' => fake()->randomElement(['Deals', 'Contacts', 'Leads', 'Activities', 'Cases', 'Campaigns', 'Invoices', 'Quotes', 'Products']),
            'filters' => [],
            'group_by' => null,
            'metrics' => ['count'],
            'date_field' => 'created_at',
            'date_range' => 'This Month',
            'custom_date_from' => null,
            'custom_date_to' => null,
            'is_scheduled' => false,
            'schedule_frequency' => null,
            'owner_id' => User::query()->value('id'),
            'is_public' => false,
        ];
    }
}
