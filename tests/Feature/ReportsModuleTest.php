<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Reports\Livewire\ReportBuilder;
use Modules\Reports\Models\Report;
use Modules\Users\Models\Role;

function makeReportsRole(): Role
{
    return Role::query()->create([
        'name' => 'Reports Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => [
            'reports' => true,
            'deals' => true,
            'contacts' => true,
            'leads' => true,
            'activities' => true,
            'cases' => true,
            'campaigns' => true,
            'invoices' => true,
            'quotes' => true,
            'products' => true,
            'users' => true,
            'core' => true,
        ],
    ]);
}

test('authorized users can open report index and dashboard', function () {
    $role = makeReportsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Reports');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('report builder creates a report', function () {
    $role = makeReportsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(ReportBuilder::class)
        ->set('name', 'Deals by status')
        ->set('description', 'Operations overview')
        ->set('type', 'Bar')
        ->set('module', 'Deals')
        ->set('group_by', 'status')
        ->set('metrics', 'count')
        ->set('date_field', 'created_at')
        ->set('date_range', 'This Month')
        ->set('filters_json', '{}')
        ->call('save');

    $this->assertDatabaseHas('reports', [
        'name' => 'Deals by status',
        'module' => 'Deals',
        'owner_id' => $user->id,
    ]);
});

test('reports can be exported as csv', function () {
    $role = makeReportsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $report = Report::query()->create([
        'name' => 'Export Test',
        'description' => null,
        'type' => 'Table',
        'module' => 'Deals',
        'filters' => [],
        'group_by' => null,
        'metrics' => ['count'],
        'date_field' => 'created_at',
        'date_range' => 'This Month',
        'custom_date_from' => null,
        'custom_date_to' => null,
        'is_scheduled' => false,
        'schedule_frequency' => null,
        'owner_id' => $user->id,
        'is_public' => false,
    ]);

    $response = $this->actingAs($user)
        ->get(route('reports.export', ['id' => $report->id]));

    $response->assertOk();

    expect((string) $response->headers->get('content-type'))->toContain('text/csv');
});
