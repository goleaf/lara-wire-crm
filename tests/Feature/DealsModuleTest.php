<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Livewire\DealForm;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;
use Modules\Users\Models\Role;

function makeDealsRole(): Role
{
    return Role::query()->create([
        'name' => 'Deals Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['deals' => true, 'contacts' => true, 'products' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open deals index', function () {
    $role = makeDealsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('deals.index'))
        ->assertOk()
        ->assertSee('Deal Pipeline');
});

test('deal form creates a deal', function () {
    $role = makeDealsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Oceanic',
        'industry' => 'Technology',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $contact = Contact::query()->create([
        'first_name' => 'Ana',
        'last_name' => 'Bell',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'lead_source' => 'Referral',
        'preferred_channel' => 'Phone',
    ]);

    $pipeline = Pipeline::query()->create([
        'name' => 'Sales',
        'is_default' => true,
        'owner_id' => $user->id,
    ]);

    $stage = PipelineStage::query()->create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Prospecting',
        'order' => 1,
        'probability' => 10,
        'color' => '#64748b',
    ]);

    $this->actingAs($user);

    Livewire::test(DealForm::class)
        ->set('name', 'Oceanic Renewal')
        ->set('accountId', $account->id)
        ->set('contactId', $contact->id)
        ->set('ownerId', $user->id)
        ->set('pipelineId', $pipeline->id)
        ->set('stageId', $stage->id)
        ->set('amount', 10000)
        ->set('probability', 25)
        ->set('dealType', 'Renewal')
        ->call('save');

    $this->assertDatabaseHas('deals', [
        'name' => 'Oceanic Renewal',
        'account_id' => $account->id,
        'pipeline_id' => $pipeline->id,
        'stage_id' => $stage->id,
    ]);
});

test('mark won endpoint moves deal to closed won stage', function () {
    $role = makeDealsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Atlas',
        'industry' => 'Technology',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $pipeline = Pipeline::query()->create([
        'name' => 'Main',
        'is_default' => true,
        'owner_id' => $user->id,
    ]);

    $prospecting = PipelineStage::query()->create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Prospecting',
        'order' => 1,
        'probability' => 10,
        'color' => '#64748b',
    ]);

    $won = PipelineStage::query()->create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Closed Won',
        'order' => 2,
        'probability' => 100,
        'color' => '#10b981',
    ]);

    $deal = Deal::query()->create([
        'name' => 'Atlas New',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'pipeline_id' => $pipeline->id,
        'stage_id' => $prospecting->id,
        'amount' => 1000,
        'currency' => 'USD',
        'probability' => 10,
        'expected_revenue' => 100,
        'deal_type' => 'New Business',
    ]);

    $this->actingAs($user)
        ->patch(route('deals.won', $deal->id))
        ->assertRedirect();

    $deal->refresh();

    expect($deal->stage_id)->toBe($won->id);
    expect($deal->closed_at)->not->toBeNull();
});

test('deal create page shows product table column headers', function () {
    $role = makeDealsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('deals.create'))
        ->assertOk()
        ->assertSee('Products')
        ->assertSee('Product')
        ->assertSee('Qty')
        ->assertSee('Unit Price')
        ->assertSee('Discount %')
        ->assertSee('Line Total')
        ->assertSee('Actions');
});

test('deal create page renders autocomplete dropdown inputs', function () {
    $role = makeDealsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('deals.create'))
        ->assertOk()
        ->assertSee('Type to search account...')
        ->assertSee('Type to search contact...')
        ->assertSee('Type to search owner...')
        ->assertSee('Type to search pipeline...')
        ->assertSee('Type to search stage...')
        ->assertSee('Type to search deal type...')
        ->assertSee('Type to search product...')
        ->assertSee('deal-account-options');
});
