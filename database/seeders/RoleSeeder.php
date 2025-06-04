<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $issuerRole = Role::create(['name' => 'issuer']);
        $investorRole = Role::create(['name' => 'investor']);

        // Create permissions
        $manageUsers = Permission::create(['name' => 'manage users']);
        $manageMeetings = Permission::create(['name' => 'manage meetings']);
        $manageRooms = Permission::create(['name' => 'manage rooms']);
        $viewMeetings = Permission::create(['name' => 'view meetings']);
        $askQuestions = Permission::create(['name' => 'ask questions']);

        // Assign permissions to roles
        $adminRole->givePermissionTo([
            'manage users',
            'manage meetings',
            'manage rooms',
            'view meetings',
            'ask questions',
        ]);

        $issuerRole->givePermissionTo([
            'view meetings',
        ]);

        $investorRole->givePermissionTo([
            'view meetings',
            'ask questions',
        ]);
    }
}
