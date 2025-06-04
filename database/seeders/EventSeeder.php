<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main conference event
        Event::create([
            'name' => 'BMCE Invest Conference 2025',
            'description' => 'Annual investment conference for issuers and investors',
            'start_date' => '2025-07-29',
            'end_date' => '2025-07-30',
            'location' => 'Casablanca, Morocco',
            'is_active' => true,
        ]);

        Event::create([
            'name' => 'Financial Markets Workshop',
            'description' => 'Workshop for financial market participants',
            'start_date' => '2025-09-15',
            'end_date' => '2025-09-17',
            'location' => 'Rabat, Morocco',
            'is_active' => false,
        ]);

        Event::create([
            'name' => 'Investment Roadshow',
            'description' => 'Roadshow for international investors',
            'start_date' => '2025-11-20',
            'end_date' => '2025-11-22',
            'location' => 'Marrakech, Morocco',
            'is_active' => false,
        ]);
    }
}
