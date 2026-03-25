<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Cases\Livewire\CaseForm;
use Modules\Cases\Models\SupportCase;
use Modules\Users\Models\Role;

function makeCasesRole(): Role
{
    return Role::query()->create([
        'name' => 'Cases Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'cases' => true,
            'contacts' => true,
            'deals' => true,
            'users' => true,
            'core' => true,
        ],
    ]);
}

test('authorized users can open cases index and support dashboard', function () {
    $role = makeCasesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('cases.index'))
        ->assertOk()
        ->assertSee('Support Cases');

    $this->actingAs($user)
        ->get(route('cases.dashboard'))
        ->assertOk()
        ->assertSee('Support Dashboard');
});

test('case form creates a support case', function () {
    $role = makeCasesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(CaseForm::class)
        ->set('title', 'API timeout in billing endpoint')
        ->set('description', 'Customer reports intermittent timeout around 17:00 UTC.')
        ->set('status', 'Open')
        ->set('priority', 'High')
        ->set('type', 'Bug')
        ->set('channel', 'Internal Portal')
        ->set('owner_id', $user->id)
        ->call('save');

    $this->assertDatabaseHas('cases', [
        'title' => 'API timeout in billing endpoint',
        'priority' => 'High',
        'owner_id' => $user->id,
    ]);
});

test('posting first comment sets first response timestamp', function () {
    $role = makeCasesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $supportCase = SupportCase::query()->create([
        'title' => 'Payment widget issue',
        'description' => 'Button does not submit for one browser.',
        'status' => 'Open',
        'priority' => 'Medium',
        'type' => 'Bug',
        'owner_id' => $user->id,
        'channel' => 'Phone',
    ]);

    $this->actingAs($user)
        ->post(route('cases.comments.store', ['id' => $supportCase->id]), [
            'body' => 'Investigating and collecting logs.',
            'is_internal' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('case_comments', [
        'case_id' => $supportCase->id,
        'user_id' => $user->id,
    ]);

    expect($supportCase->fresh()->first_response_at)->not->toBeNull();
});

test('updating case status to resolved sets resolved timestamp', function () {
    $role = makeCasesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $supportCase = SupportCase::query()->create([
        'title' => 'Sync backlog',
        'description' => 'Batch retries stuck in pending.',
        'status' => 'Open',
        'priority' => 'Low',
        'type' => 'Question',
        'owner_id' => $user->id,
        'channel' => 'Other',
    ]);

    $this->actingAs($user)
        ->patch(route('cases.status', ['id' => $supportCase->id]), [
            'status' => 'Resolved',
        ])
        ->assertRedirect();

    $resolvedCase = $supportCase->fresh();

    expect($resolvedCase?->status)->toBe('Resolved');
    expect($resolvedCase?->resolved_at)->not->toBeNull();
});

test('check-sla command runs and detects breached cases', function () {
    $role = makeCasesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $supportCase = SupportCase::query()->create([
        'title' => 'SLA breach sample',
        'description' => 'Case should be picked up by SLA checker.',
        'status' => 'Open',
        'priority' => 'Critical',
        'type' => 'Complaint',
        'owner_id' => $user->id,
        'channel' => 'Internal Portal',
    ]);

    $supportCase->forceFill([
        'sla_deadline' => now()->subHour(),
    ])->saveQuietly();

    $this->artisan('cases:check-sla')
        ->expectsOutputToContain('SLA-breached cases found: 1')
        ->assertSuccessful();
});
