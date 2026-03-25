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
        $invoiceId = Invoice::query()->value('id') ?? Invoice::factory()->create()->getKey();
        $recordedBy = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'invoice_id' => (string) $invoiceId,
            'amount' => fake()->randomFloat(2, 20, 800),
            'paid_at' => now()->subDays(fake()->numberBetween(0, 10))->toDateString(),
            'method' => fake()->randomElement(['Bank Transfer', 'Cash', 'Cheque', 'Internal Credit']),
            'reference' => fake()->bothify('PAY-####'),
            'recorded_by' => (string) $recordedBy,
            'notes' => fake()->sentence(),
        ];
    }
}
