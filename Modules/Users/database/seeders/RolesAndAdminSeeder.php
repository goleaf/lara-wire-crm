<?php

namespace Modules\Users\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Users\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
                'can_export' => true,
                'record_visibility' => 'all',
                'module_access' => [
                    'users' => true,
                    'core' => true,
                ],
            ],
            [
                'name' => 'Manager',
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => false,
                'can_export' => true,
                'record_visibility' => 'team',
                'module_access' => [
                    'users' => true,
                    'core' => true,
                ],
            ],
            [
                'name' => 'Sales Rep',
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => false,
                'can_export' => false,
                'record_visibility' => 'own',
                'module_access' => [
                    'users' => false,
                    'core' => true,
                ],
            ],
            [
                'name' => 'Viewer',
                'can_view' => true,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_export' => false,
                'record_visibility' => 'own',
                'module_access' => [
                    'users' => false,
                    'core' => true,
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::query()->updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $adminRole = Role::query()->where('name', 'Admin')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'admin@crm.local'],
            [
                'full_name' => 'System Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->getKey(),
                'is_active' => true,
                'last_login' => now()->subDay(),
                'quota' => 0,
                'avatar_path' => 'avatars/system-admin.png',
            ]
        );

        $defaultUserRole = Role::query()->where('name', 'Sales Rep')->first() ?? $adminRole;
        $demoUsers = collect(config('crm.demo_login_users', []));
        $demoPassword = (string) config('crm.demo_login_password', 'password123');

        $demoUsers
            ->filter(fn (array $demoUser): bool => filled($demoUser['email'] ?? null))
            ->each(function (array $demoUser) use ($defaultUserRole, $demoPassword): void {
                $email = (string) $demoUser['email'];

                User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'full_name' => $demoUser['full_name'] ?? 'Demo User',
                        'password' => Hash::make($demoPassword),
                        'role_id' => $defaultUserRole->getKey(),
                        'is_active' => true,
                        'last_login' => now()->subMinutes(random_int(5, 600)),
                        'quota' => 0,
                        'avatar_path' => 'avatars/'.Str::slug(Str::before($email, '@')).'.png',
                    ]
                );
            });
    }
}
