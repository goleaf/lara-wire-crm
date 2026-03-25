<?php

use App\Models\User;

test('authenticated users see the core dashboard card', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('CRM Ready')
        ->assertSee('Core');
});

test('crm currency helper returns the configured symbol', function () {
    expect(crm_currency())->toBe('$');
});
