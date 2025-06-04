<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all issuer users
        $issuers = User::role(UserRole::ISSUER->value)->get();

        if ($issuers->isEmpty()) {
            $this->command->error("Aucun émetteur trouvé. Créez d'abord des utilisateurs émetteurs.");
            return;
        }

        // Get all active events
        $activeEvents = Event::active()->get();

        if ($activeEvents->isEmpty()) {
            $this->command->info("Aucun événement actif trouvé. Utilisation des dates par défaut.");
            // Default conference dates: June 12-13, 2025
            $defaultDates = [
                '2025-06-12',
                '2025-06-13'
            ];

            // Create default time slots for all issuers
            foreach ($issuers as $issuer) {
                $this->createTimeSlotsByIssuer($issuer, $defaultDates);
            }

            $this->command->info("Time slots créés avec succès pour " . $issuers->count() . " émetteurs (mode par défaut)");
        } else {
            $eventCount = $activeEvents->count();
            $this->command->info("Création des time slots pour {$eventCount} événement(s) actif(s)");

            // Create time slots for each active event
            foreach ($activeEvents as $event) {
                $dates = $event->getDatesArray();
                $this->command->line("- Création des time slots pour l'événement: {$event->name} ({$event->start_date} - {$event->end_date})");

                // Create time slots for all issuers for this event
                foreach ($issuers as $issuer) {
                    $this->createTimeSlotsByIssuer($issuer, $dates, $event);
                }
            }

            $this->command->info("Time slots créés avec succès pour " . $issuers->count() . " émetteurs");
        }
    }

    /**
     * Create time slots for a specific issuer on given conference dates.
     *
     * @param User $issuer The issuer user
     * @param array $dates Array of date strings
     * @param Event|null $event The event object (optional)
     */
    private function createTimeSlotsByIssuer(User $issuer, array $dates, ?Event $event = null)
    {
        // Check if user already has time slots for this event
        $query = TimeSlot::where('user_id', $issuer->id);
        if ($event) {
            $query->where('event_id', $event->id);
        } else {
            $query->whereNull('event_id');
        }

        if ($query->exists()) {
            $this->command->comment("  - Time slots pour {$issuer->first_name} {$issuer->name} déjà existants" .
                ($event ? " pour l'événement {$event->name}" : " (default)"));
            return;
        }

        // Define time slots for each day (45-minute intervals)
        $timeSlots = [
            ['09:00', '09:45'],
            ['10:00', '10:45'],
            ['11:00', '11:45'],
            ['12:00', '12:45'],
            ['14:00', '14:45'],
            ['15:00', '15:45'],
            ['16:00', '16:45'],
            ['17:00', '17:45']
        ];

        $timeSlotsToCreate = [];

        foreach ($dates as $date) {
            foreach ($timeSlots as [$startTime, $endTime]) {
                // Pour les données de test, on met certains créneaux à disponible (true)
                // Dans le workflow réel, tous seraient initialement à false
                $isAvailable = rand(1, 100) <= 50; // 50% disponibilité pour les tests

                $timeSlotsToCreate[] = [
                    'user_id' => $issuer->id,
                    'event_id' => $event ? $event->id : null,
                    'date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'availability' => $isAvailable, // Pour les tests, certains créneaux sont disponibles
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insertion en masse pour de meilleures performances
        if (!empty($timeSlotsToCreate)) {
            TimeSlot::insert($timeSlotsToCreate);
            $slotsCount = count($timeSlotsToCreate);

            $this->command->comment("  - {$slotsCount} time slots créés pour {$issuer->first_name} {$issuer->name}" .
                ($event ? " pour l'événement {$event->name}" : " (default)"));
        }
    }
}
