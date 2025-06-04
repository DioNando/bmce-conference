<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Virtual Meeting',
                'capacity' => 100,
                'location' => 'Online',
            ],
            [
                'name' => 'Chellah',
                'capacity' => 20,
                'location' => 'Four Seasons Hotel, 1st Floor',
            ],
            [
                'name' => 'Cotta',
                'capacity' => 15,
                'location' => 'Four Seasons Hotel, 1st Floor',
            ],
            [
                'name' => 'Lixus',
                'capacity' => 12,
                'location' => 'Four Seasons Hotel, 2nd Floor',
            ],
            [
                'name' => 'Volubilis',
                'capacity' => 10,
                'location' => 'Four Seasons Hotel, 2nd Floor',
            ],
            [
                'name' => 'Tingis',
                'capacity' => 8,
                'location' => 'Four Seasons Hotel, 3rd Floor',
            ],
            [
                'name' => 'Mogador',
                'capacity' => 6,
                'location' => 'Four Seasons Hotel, 3rd Floor',
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
