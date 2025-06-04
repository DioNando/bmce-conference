<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use OCILob;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            RoomSeeder::class,
            OrganizationSeeder::class,
            UserSeeder::class,
            EventSeeder::class,
            TimeSlotSeeder::class,
            MeetingSeeder::class,
        ]);
    }
}
