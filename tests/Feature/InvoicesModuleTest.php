<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Invoices\Livewire\InvoiceForm;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceLineItem;
use Modules\Users\Models\Role;

function makeInvoicesRole(): Role
{
    return Role::query()->create([
        'name' => 'Invoices Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'invoices' => true,
            'quotes' => true,
            'deals' => true,
            'contacts' => true,
            'users' => true,
            'core' => true,
        ],
    ]);
}

test('authorized users can open invoices index', function () {
    $role = makeInvoicesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('invoices.index'))
        ->assertOk()
        ->assertSee('Invoices');
});

test('invoice form creates an issued invoice with line items', function () {
    $role = makeInvoicesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Bluebird Co',
        'industry' => 'Technology',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $contact = Contact::query()->create([
        'first_name' => 'Iris',
        'last_name' => 'Stone',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'lead_source' => 'Referral',
        'preferred_channel' => 'Phone',
    ]);

    $this->actingAs($user);

    Livewire::test(InvoiceForm::class)
        ->set('account_id', $account->id)
        ->set('contact_id', $contact->id)
        ->set('owner_id', $user->id)
        ->set('issue_date', now()->toDateString())
        ->set('due_date', now()->addDays(30)->toDateString())
        ->set('lineItems', [[
            'product_id' => null,
            'name' => 'Consulting Package',
            'quantity' => 2,
            'unit_price' => 250,
            'discount' => 10,
            'tax_rate' => 21,
            'total' => 450,
        ]])
        ->call('saveIssued');

    $invoice = Invoice::query()->first();

    expect($invoice)->not->toBeNull();
    expect($invoice?->status)->toBe('Issued');

    $this->assertDatabaseHas('invoice_line_items', [
        'invoice_id' => $invoice?->id,
        'name' => 'Consulting Package',
    ]);
});

test('record payment endpoint updates totals and status', function () {
    $role = makeInvoicesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Northwind',
        'industry' => 'Finance',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $invoice = Invoice::query()->create([
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'status' => 'Issued',
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDays(20)->toDateString(),
        'discount_type' => 'Percentage',
        'discount_value' => 0,
        'currency' => 'USD',
    ]);

    InvoiceLineItem::query()->create([
        'invoice_id' => $invoice->id,
        'product_id' => null,
        'name' => 'Subscription',
        'quantity' => 1,
        'unit_price' => 1000,
        'discount_percent' => 0,
        'tax_rate' => 0,
        'total' => 1000,
        'order' => 0,
    ]);

    $invoice->recalculate();

    $this->actingAs($user)
        ->post(route('invoices.payment', $invoice->id), [
            'amount' => 400,
            'paid_at' => now()->toDateString(),
            'method' => 'Bank Transfer',
            'reference' => 'TRX-10',
            'notes' => 'First installment',
        ])
        ->assertRedirect();

    $invoice->refresh();

    expect((float) $invoice->amount_paid)->toBe(400.0);
    expect($invoice->status)->toBe('Partially Paid');
});

test('check overdue command marks issued invoices as overdue', function () {
    $role = makeInvoicesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Legacy Corp',
        'industry' => 'Retail',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $invoice = Invoice::query()->create([
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'status' => 'Issued',
        'issue_date' => now()->subDays(20)->toDateString(),
        'due_date' => now()->subDays(2)->toDateString(),
        'discount_type' => 'Percentage',
        'discount_value' => 0,
        'currency' => 'USD',
    ]);

    $this->artisan('invoices:check-overdue')
        ->assertSuccessful();

    expect($invoice->fresh()->status)->toBe('Overdue');
});
