<?php

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 600000000',
            'position' => 'Administrator',
            'status' => true,
        ]);

        $admin->assignRole(UserRole::ADMIN->value);

        // Example Issuer user
        $issuerExample = User::create([
            'first_name' => 'Example',
            'name' => 'Issuer',
            'email' => 'issuer@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 611111111',
            'position' => 'Finance Director',
            'status' => true,
        ]);

        $issuerExample->assignRole(UserRole::ISSUER->value);

        // Example Investor user
        $investorExample = User::create([
            'first_name' => 'Example',
            'name' => 'Investor',
            'email' => 'investor@example.com',
            'password' => Hash::make('password'),
            'phone' => '+212 622222222',
            'position' => 'Portfolio Manager',
            'status' => true,
        ]);

        $investorExample->assignRole(UserRole::INVESTOR->value);
    }
}
