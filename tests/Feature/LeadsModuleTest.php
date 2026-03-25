<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Leads\Livewire\LeadForm;
use Modules\Leads\Models\Lead;
use Modules\Users\Models\Role;

function makeLeadsRole(): Role
{
    return Role::query()->create([
        'name' => 'Leads Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['leads' => true, 'contacts' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open leads index', function () {
    $role = makeLeadsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('leads.index'))
        ->assertOk()
        ->assertSee('Leads');
});

test('lead form creates lead with calculated score', function () {
    $role = makeLeadsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(LeadForm::class)
        ->set('firstName', 'Nora')
        ->set('lastName', 'Stone')
        ->set('email', 'nora@example.test')
        ->set('phone', '+37060000000')
        ->set('company', 'Stone Labs')
        ->set('leadSource', 'Referral')
        ->set('status', 'Qualified')
        ->set('rating', 'Hot')
        ->set('ownerId', $user->id)
        ->call('save');

    $lead = Lead::query()->where('email', 'nora@example.test')->firstOrFail();

    expect($lead->score)->toBe(90);
});

test('convert endpoint marks lead converted and creates contact', function () {
    $role = makeLeadsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $lead = Lead::query()->create([
        'first_name' => 'Alex',
        'last_name' => 'Turner',
        'company' => 'AT Corp',
        'email' => 'alex@example.test',
        'phone' => '123456',
        'lead_source' => 'Event',
        'status' => 'Contacted',
        'score' => 0,
        'rating' => 'Warm',
        'owner_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('leads.convert', $lead->id))
        ->assertRedirect(route('leads.show', $lead->id));

    $lead->refresh();

    expect($lead->converted)->toBeTrue();
    expect($lead->converted_to_contact_id)->not->toBeNull();

    $this->assertDatabaseHas('contacts', [
        'id' => $lead->converted_to_contact_id,
        'email' => 'alex@example.test',
    ]);
});
