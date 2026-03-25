<?php

namespace Modules\Invoices\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Quotes\Models\Quote;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['Draft', 'Issued', 'Partially Paid', 'Paid', 'Overdue']);
        $total = (float) fake()->randomFloat(2, 200, 15000);
        $accountId = Account::query()->value('id') ?? Account::factory()->create()->getKey();
        $ownerId = User::query()->value('id') ?? User::factory()->create()->getKey();

        return [
            'number' => sprintf('INV-%s-%04d', now()->format('Y'), fake()->unique()->numberBetween(1, 9999)),
            'quote_id' => Quote::query()->value('id'),
            'deal_id' => Deal::query()->value('id'),
            'account_id' => (string) $accountId,
            'contact_id' => Contact::query()->value('id'),
            'owner_id' => (string) $ownerId,
            'status' => $status,
            'issue_date' => now()->subDays(fake()->numberBetween(0, 30))->toDateString(),
            'due_date' => now()->addDays(fake()->numberBetween(-20, 45))->toDateString(),
            'notes' => fake()->sentence(),
            'internal_notes' => fake()->sentence(),
            'subtotal' => $total,
            'discount_type' => fake()->randomElement(['Percentage', 'Fixed']),
            'discount_value' => fake()->randomFloat(2, 0, 10),
            'discount_amount' => fake()->randomFloat(2, 0, 50),
            'tax_amount' => fake()->randomFloat(2, 0, 120),
            'total' => $total,
            'amount_paid' => match ($status) {
                'Paid' => $total,
                'Partially Paid' => round($total * fake()->randomFloat(2, 0.1, 0.8), 2),
                default => 0,
            },
            'currency' => config('crm.default_currency.code', 'USD'),
            'pdf_path' => 'crm-pdfs/invoices/'.fake()->uuid().'.pdf',
        ];
    }
}
