<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder {
    public function run(): void {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage inventory',
            'view financial reports',
            'process orders',
            'manage settings',
            'manage users',
            'request inventory audit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Owner']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo(['manage inventory', 'process orders', 'view financial reports']);

        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->givePermissionTo(['process orders']);
    }
}
