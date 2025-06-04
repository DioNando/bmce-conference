<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateIssuerTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issuers:generate-timeslots {--user-id= : Specific user ID to generate time slots for} {--force : Force regeneration even if time slots already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate default time slots for all issuer users or a specific issuer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $force = $this->option('force');

        if ($userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }

            if (!$user->hasRole(UserRole::ISSUER->value)) {
                $this->error("User with ID {$userId} is not an issuer.");
                return 1;
            }

            // Check if the user already has time slots
            if (!$force && $user->timeSlots()->exists()) {
                $this->warn("User {$user->name} (ID: {$user->id}) already has time slots. Use --force to regenerate.");
                $this->info("If you want to regenerate time slots, use: php artisan issuers:generate-timeslots --user-id={$user->id} --force");
                return 0;
            }

            $this->generateTimeSlotsForUser($user, $force);
            $this->info("TimeSlots generated for user {$user->name} (ID: {$user->id}).");

            return 0;
        }

        // Get all issuer users without time slots (or all if force is true)
        $query = User::role(UserRole::ISSUER->value);

        if (!$force) {
            $query->whereDoesntHave('timeSlots');
        }

        $issuers = $query->get();

        if ($issuers->count() === 0) {
            $this->info('All issuers already have time slots. Use --force to regenerate.');
            return 0;
        }

        $bar = $this->output->createProgressBar($issuers->count());
        $bar->start();

        foreach ($issuers as $issuer) {
            $this->generateTimeSlotsForUser($issuer, $force);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("TimeSlots generated for {$issuers->count()} issuers.");

        return 0;
    }

    /**
     * Generate time slots for a specific user
     */
    private function generateTimeSlotsForUser(User $user, bool $force = false): void
    {
        try {
            // If force is true, delete existing time slots first
            if ($force) {
                $existingCount = $user->timeSlots()->count();
                if ($existingCount > 0) {
                    // Check if there are any meetings associated with the time slots
                    $hasBookings = $user->timeSlots()->whereHas('meetings')->exists();

                    if ($hasBookings) {
                        $this->warn("Skipping user {$user->name} (ID: {$user->id}) - has time slots with meetings.");
                        return;
                    }

                    $user->timeSlots()->delete();
                    Log::info("Deleted {$existingCount} existing time slots for issuer ID: {$user->id}");
                }
            } else if ($user->timeSlots()->exists()) {
                // If not force and user has time slots, skip
                return;
            }

            // Define conference dates (June 12-13, 2025)
            $conferenceDates = [
                '2025-06-12',
                '2025-06-13'
            ];

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

            foreach ($conferenceDates as $date) {
                foreach ($timeSlots as [$startTime, $endTime]) {
                    $timeSlotsToCreate[] = [
                        'user_id' => $user->id,
                        'date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'availability' => false, // Default to not available
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // Bulk insert all time slots
            TimeSlot::insert($timeSlotsToCreate);

            Log::info("Created " . count($timeSlotsToCreate) . " default time slots for issuer ID: " . $user->id);
        } catch (\Exception $e) {
            Log::error("Failed to create default time slots for user ID: " . $user->id . ". Error: " . $e->getMessage());
            $this->error("Error creating time slots for user {$user->name}: " . $e->getMessage());
        }
    }
}
