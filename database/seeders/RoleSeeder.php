<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder {

    public function run(): void {

        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Granular Permissions
        $permissionGroups = [
            'inventory' => ['view inventory', 'create inventory', 'edit inventory', 'delete inventory', 'audit inventory'],
            'orders' => ['view orders', 'create orders', 'edit orders', 'cancel orders', 'refund orders'],
            'finance' => ['view reports', 'export data', 'manage tax settings'],
            'users' => ['view users', 'create users', 'edit users', 'delete users', 'assign roles'],
            'settings' => ['manage system settings', 'view logs'],
        ];

        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        // 3. Create Roles & Assign Permissions

        // Owner (Super Admin) - Has everything
        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->syncPermissions(Permission::all());

        // Manager - Operational control but no system settings/deleting users
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions([
            'view inventory', 'create inventory', 'edit inventory', 'audit inventory',
            'view orders', 'create orders', 'edit orders', 'cancel orders',
            'view reports', 'view users'
        ]);

        // Accountant - Financial focus
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions(['view reports', 'export data', 'view orders', 'refund orders']);

        // Staff / Sales Associate - Daily operations
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->syncPermissions(['view inventory', 'view orders', 'create orders']);

        // Auditor - View only access for compliance
        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->syncPermissions(['view inventory', 'view orders', 'view reports', 'view logs']);

        // 4. Create Demo Users and Assign Roles
        $this->createUser('System Owner', 'owner@business.com', 'Owner');
        $this->createUser('Operations Manager', 'manager@business.com', 'Manager');
        $this->createUser('Finance Lead', 'finance@business.com', 'Accountant');
        $this->createUser('Junior Staff', 'staff@business.com', 'Staff');

        $this->command->info('Roles, Permissions, and Demo Users seeded successfully!');
    }

    /**
     * Helper to create user and assign role
     */
    private function createUser($name, $email, $roleName): void {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password123'), // Default password
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($roleName);
    }
}