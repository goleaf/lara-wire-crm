<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\Users\Database\Seeders\RolesAndAdminSeeder;
use Modules\Users\Models\Role;

function makeUsersRole(string $name = 'Admin', array $overrides = []): Role
{
    return Role::query()->create(array_merge([
        'name' => $name,
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['users' => true, 'core' => true],
    ], $overrides));
}

test('authorized users can open users index', function () {
    $role = makeUsersRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Users');
});

test('inactive users are logged out by active middleware', function () {
    $role = makeUsersRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('successful login records last login timestamp', function () {
    $role = makeUsersRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
        'password' => Hash::make('password'),
        'last_login' => null,
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->last_login)->not->toBeNull();
});

test('users seeder creates five demo users with shared password', function () {
    $this->seed(RolesAndAdminSeeder::class);

    $emails = [
        'user1@example.com',
        'user2@example.com',
        'user3@example.com',
        'user4@example.com',
        'user5@example.com',
    ];

    expect(User::query()->whereIn('email', $emails)->count())->toBe(5);

    foreach ($emails as $email) {
        $seededUser = User::query()->where('email', $email)->first();

        expect($seededUser)->not->toBeNull();
        expect(Hash::check('password123', (string) $seededUser?->password))->toBeTrue();
    }
});

test('profile page includes appearance theme switcher', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('users.profile'))
        ->assertOk()
        ->assertSee('Appearance')
        ->assertSee('Light')
        ->assertSee('Dark')
        ->assertSee('x-model="$flux.appearance"', false);
});
