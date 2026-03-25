<?php

namespace Modules\Invoices\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::query()->select(['id'])->get();
        $accounts = Account::query()->select(['id'])->get();

        if ($owners->isEmpty() || $accounts->isEmpty()) {
            return;
        }

        $deals = Deal::query()->select(['id'])->get();
        $contacts = Contact::query()->select(['id'])->get();
        $quotes = Quote::query()->select(['id'])->get();
        $products = Product::query()->select(['id', 'name', 'unit_price', 'tax_rate'])->get();
        $statuses = ['Draft', 'Issued', 'Partially Paid', 'Paid', 'Overdue'];

        for ($index = 1; $index <= 10; $index++) {
            $status = $statuses[array_rand($statuses)];

            $invoice = Invoice::query()->create([
                'quote_id' => $quotes->isNotEmpty() ? $quotes->random()->id : null,
                'deal_id' => $deals->isNotEmpty() ? $deals->random()->id : null,
                'account_id' => $accounts->random()->id,
                'contact_id' => $contacts->isNotEmpty() ? $contacts->random()->id : null,
                'owner_id' => $owners->random()->id,
                'status' => in_array($status, ['Partially Paid', 'Paid', 'Overdue'], true) ? 'Issued' : $status,
                'issue_date' => now()->subDays(random_int(0, 45))->toDateString(),
                'due_date' => $status === 'Overdue'
                    ? now()->subDays(random_int(1, 35))->toDateString()
                    : now()->addDays(random_int(5, 40))->toDateString(),
                'notes' => 'Payment due as per terms.',
                'internal_notes' => 'Seeded invoice '.$index,
                'discount_type' => 'Percentage',
                'discount_value' => random_int(0, 10),
                'currency' => config('crm.default_currency.code', 'USD'),
            ]);

            $lineCount = random_int(1, 3);

            for ($line = 0; $line < $lineCount; $line++) {
                $product = $products->isNotEmpty() ? $products->random() : null;
                $quantity = random_int(1, 5);
                $unitPrice = $product ? (float) $product->unit_price : (float) random_int(80, 550);
                $discount = random_int(0, 15);
                $taxRate = $product ? (float) $product->tax_rate : 21;

                $invoice->lineItems()->create([
                    'product_id' => $product?->id,
                    'name' => $product?->name ?? 'Service Line '.($line + 1),
                    'description' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discount,
                    'tax_rate' => $taxRate,
                    'total' => round($quantity * $unitPrice * (1 - ($discount / 100)), 2),
                    'order' => $line,
                ]);
            }

            $invoice->recalculate();

            if ($status === 'Paid' && $invoice->total > 0) {
                $invoice->recordPayment([
                    'amount' => (float) $invoice->total,
                    'paid_at' => now()->subDays(random_int(0, 7))->toDateString(),
                    'method' => 'Bank Transfer',
                    'reference' => 'SEED-PAID-'.$index,
                    'notes' => null,
                    'recorded_by' => $invoice->owner_id,
                ]);
            }

            if ($status === 'Partially Paid' && $invoice->total > 0) {
                $invoice->recordPayment([
                    'amount' => round((float) $invoice->total * 0.45, 2),
                    'paid_at' => now()->subDays(random_int(0, 7))->toDateString(),
                    'method' => 'Cash',
                    'reference' => 'SEED-PART-'.$index,
                    'notes' => null,
                    'recorded_by' => $invoice->owner_id,
                ]);
            }

            if ($status === 'Overdue') {
                $invoice->forceFill(['status' => 'Overdue'])->save();
            }
        }
    }
}
