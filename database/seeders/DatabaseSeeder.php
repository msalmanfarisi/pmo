<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage settings',
            'manage users',
            'manage projects',
            'manage sprints',
            'manage tasks',
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            'manage projects',
            'manage sprints',
            'manage tasks',
            'view reports',
            'export reports',
        ]);

        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $memberRole->syncPermissions([
            'manage tasks',
            'view reports',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions([
            'view reports',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@pmo.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@PMO2024!'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');
    }
}
