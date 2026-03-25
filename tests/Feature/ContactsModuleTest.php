<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Contacts\Livewire\AccountForm;
use Modules\Contacts\Livewire\ContactForm;
use Modules\Contacts\Models\Account;
use Modules\Users\Models\Role;

function makeContactsRole(): Role
{
    return Role::query()->create([
        'name' => 'Contacts Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['contacts' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open accounts index', function () {
    $role = makeContactsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('accounts.index'))
        ->assertOk()
        ->assertSee('Accounts');
});

test('users can create account from livewire form', function () {
    $role = makeContactsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(AccountForm::class)
        ->set('name', 'Acme Holdings')
        ->set('industry', 'Technology')
        ->set('type', 'Customer')
        ->set('website', 'https://acme.test')
        ->set('phone', '123456')
        ->set('email', 'hello@acme.test')
        ->set('ownerId', $user->id)
        ->set('billingAddress.street', 'Main 1')
        ->set('billingAddress.city', 'Vilnius')
        ->set('billingAddress.state', 'LT')
        ->set('billingAddress.zip', '01001')
        ->set('billingAddress.country', 'Lithuania')
        ->call('save');

    $this->assertDatabaseHas('accounts', [
        'name' => 'Acme Holdings',
        'type' => 'Customer',
        'owner_id' => $user->id,
    ]);
});

test('users can create contact from livewire form', function () {
    $role = makeContactsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Northwind',
        'industry' => 'Technology',
        'type' => 'Customer',
        'billing_address' => [
            'street' => 'Street',
            'city' => 'City',
            'state' => 'State',
            'zip' => '1000',
            'country' => 'Country',
        ],
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ContactForm::class)
        ->set('firstName', 'John')
        ->set('lastName', 'Doe')
        ->set('email', 'john@example.test')
        ->set('phone', '111222')
        ->set('accountId', $account->id)
        ->set('ownerId', $user->id)
        ->set('leadSource', 'Referral')
        ->set('preferredChannel', 'Phone')
        ->call('save');

    $this->assertDatabaseHas('contacts', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'account_id' => $account->id,
        'owner_id' => $user->id,
    ]);
});
