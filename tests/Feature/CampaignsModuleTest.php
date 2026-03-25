<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Campaigns\Livewire\CampaignForm;
use Modules\Campaigns\Models\Campaign;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Users\Models\Role;

function makeCampaignsRole(): Role
{
    return Role::query()->create([
        'name' => 'Campaigns Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'campaigns' => true,
            'contacts' => true,
            'leads' => true,
            'users' => true,
            'core' => true,
        ],
    ]);
}

test('authorized users can open campaigns index', function () {
    $role = makeCampaignsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('campaigns.index'))
        ->assertOk()
        ->assertSee('Campaigns');
});

test('campaign form creates a campaign', function () {
    $role = makeCampaignsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(CampaignForm::class)
        ->set('name', 'Spring Outreach')
        ->set('type', 'Event')
        ->set('status', 'Active')
        ->set('start_date', now()->toDateString())
        ->set('end_date', now()->addDays(21)->toDateString())
        ->set('budget', 10000)
        ->set('actual_cost', 3500)
        ->set('target_audience', 'SMB Finance')
        ->set('expected_leads', 120)
        ->set('description', 'Primary quarter campaign')
        ->set('owner_id', $user->id)
        ->call('save');

    $this->assertDatabaseHas('campaigns', [
        'name' => 'Spring Outreach',
        'type' => 'Event',
        'status' => 'Active',
        'owner_id' => $user->id,
    ]);
});

test('contacts can be added to campaign via endpoint', function () {
    $role = makeCampaignsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $account = Account::query()->create([
        'name' => 'Acme Support',
        'industry' => 'Technology',
        'type' => 'Customer',
        'billing_address' => ['street' => 'A', 'city' => 'B', 'state' => 'C', 'zip' => 'D', 'country' => 'E'],
        'owner_id' => $user->id,
    ]);

    $contact = Contact::query()->create([
        'first_name' => 'Liam',
        'last_name' => 'Grant',
        'account_id' => $account->id,
        'owner_id' => $user->id,
        'lead_source' => 'Referral',
        'preferred_channel' => 'Phone',
    ]);

    $campaign = Campaign::query()->create([
        'name' => 'Referral Program Alpha',
        'type' => 'Referral Program',
        'status' => 'Planned',
        'budget' => 2000,
        'actual_cost' => 200,
        'expected_leads' => 30,
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('campaigns.contacts.store', ['id' => $campaign->id]), [
            'contact_ids' => [$contact->id],
            'status' => 'Targeted',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('campaign_contacts', [
        'campaign_id' => $campaign->id,
        'contact_id' => $contact->id,
        'status' => 'Targeted',
    ]);
});
