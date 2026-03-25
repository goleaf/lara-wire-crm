<?php

namespace Modules\Invoices\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::query()->value('id'),
            'amount' => fake()->randomFloat(2, 20, 800),
            'paid_at' => now()->subDays(fake()->numberBetween(0, 10))->toDateString(),
            'method' => fake()->randomElement(['Bank Transfer', 'Cash', 'Cheque', 'Internal Credit']),
            'reference' => fake()->optional()->bothify('PAY-####'),
            'recorded_by' => User::query()->value('id'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
