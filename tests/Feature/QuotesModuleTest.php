<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Contacts\Models\Account;
use Modules\Quotes\Livewire\QuoteDetail;
use Modules\Quotes\Livewire\QuoteForm;
use Modules\Quotes\Models\Quote;
use Modules\Users\Models\Role;

function makeQuotesRole(): Role
{
    return Role::query()->create([
        'name' => 'Quotes Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['quotes' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open quotes index', function () {
    $role = makeQuotesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('quotes.index'))
        ->assertOk()
        ->assertSee('Quotes');
});

test('users can create quote from livewire form', function () {
    $role = makeQuotesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Acme Corp',
        'industry' => 'Technology',
        'type' => 'Customer',
        'owner_id' => $user->id,
        'billing_address' => [
            'street' => 'Main',
            'city' => 'NYC',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
        ],
    ]);

    $this->actingAs($user);

    Livewire::test(QuoteForm::class)
        ->set('name', 'Q2 Renewal')
        ->set('account_id', $account->id)
        ->set('owner_id', $user->id)
        ->set('currency', 'USD')
        ->set('discount_type', 'Percentage')
        ->set('discount_value', '10')
        ->set('lineItems', [[
            'product_id' => null,
            'name' => 'Consulting',
            'quantity' => 2,
            'unit_price' => 500,
            'discount' => 0,
            'tax_rate' => 10,
            'total' => 1000,
        ]])
        ->call('saveDraft');

    $quote = Quote::query()
        ->where('name', 'Q2 Renewal')
        ->first();

    expect($quote)->not->toBeNull();
    $this->assertDatabaseHas('quote_line_items', [
        'quote_id' => $quote->id,
        'name' => 'Consulting',
    ]);
});

test('pdf download endpoint returns quote pdf', function () {
    $role = makeQuotesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Northwind',
        'industry' => 'Finance',
        'type' => 'Customer',
        'owner_id' => $user->id,
        'billing_address' => [
            'street' => 'Broadway',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10002',
            'country' => 'USA',
        ],
    ]);

    $quote = Quote::query()->create([
        'name' => 'Northwind Proposal',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'status' => 'Draft',
        'currency' => 'USD',
        'discount_type' => 'Percentage',
        'discount_value' => 0,
    ]);

    $quote->lineItems()->create([
        'name' => 'Implementation',
        'quantity' => 1,
        'unit_price' => 1200,
        'discount_percent' => 0,
        'tax_rate' => 0,
        'total' => 1200,
        'order' => 0,
    ]);
    $quote->recalculate();

    $this->actingAs($user)
        ->get(route('quotes.pdf', $quote->id))
        ->assertOk()
        ->assertDownload($quote->number.'.pdf');
});

test('quote detail mark sent shows status message and updates quote status', function () {
    $role = makeQuotesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Contoso',
        'industry' => 'Technology',
        'type' => 'Customer',
        'owner_id' => $user->id,
        'billing_address' => [
            'street' => 'Main',
            'city' => 'NYC',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
        ],
    ]);

    $quote = Quote::query()->create([
        'name' => 'Contoso Renewal',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'status' => 'Draft',
        'currency' => 'USD',
        'discount_type' => 'Percentage',
        'discount_value' => 0,
    ]);

    $this->actingAs($user);

    Livewire::test(QuoteDetail::class, ['id' => $quote->id])
        ->call('markSent')
        ->assertSet('statusMessage', 'Quote marked as sent.')
        ->assertDispatched('flash', type: 'success', message: 'Quote marked as sent.');

    expect($quote->fresh()->status)->toBe('Sent');
});
