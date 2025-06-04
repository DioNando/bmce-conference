<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Log::info("User created: {$user->id} - {$user->email}");

        // Check if the user is an issuer
        if ($user->hasRole(UserRole::ISSUER->value)) {
            $this->createDefaultTimeSlots($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if the user has been assigned the issuer role and doesn't already have time slots
        if ($user->hasRole(UserRole::ISSUER->value) && !$this->userHasTimeSlots($user)) {
            Log::info("Creating time slots for updated user: {$user->id} - {$user->email}");
            $this->createDefaultTimeSlots($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // TimeSlots will be automatically deleted due to cascading delete in the migration
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    /**
     * Check if a user already has time slots
     */
    private function userHasTimeSlots(User $user): bool
    {
        return TimeSlot::where('user_id', $user->id)->exists();
    }

    /**
     * Create default time slots for conference dates for an issuer.
     */
    private function createDefaultTimeSlots(User $user): void
    {
        try {
            // // Define conference dates (June 12-13, 2025)
            // $conferenceDates = [
            //     '2025-06-12',
            //     '2025-06-13'
            // ];
            // Define conference dates (July 29-30, 2025)
            $conferenceDates = [
                '2025-07-29',
                '2025-07-30'
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
        }
    }
}
