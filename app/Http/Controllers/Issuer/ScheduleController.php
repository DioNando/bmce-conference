<?php

namespace App\Http\Controllers\Issuer;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Affiche la vue de gestion des disponibilités de l'émetteur connecté
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Vérifier s'il y a un message d'erreur dans les paramètres d'URL
        if ($request->has('error')) {
            return redirect()->route('issuer.schedule')->with('error', $request->query('error'));
        }

        // Vérifier s'il y a un message de succès dans les paramètres d'URL
        if ($request->has('success')) {
            return redirect()->route('issuer.schedule')->with('success', $request->query('success'));
        }

        // Récupérer les créneaux horaires de l'émetteur, groupés par date
        $timeSlotsByDate = $user->timeSlots()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($timeSlot) {
                return $timeSlot->date->format('Y-m-d');
            });

        // Convertir les clés de date en objets Carbon pour faciliter le formatage
        $formattedTimeSlots = [];
        foreach ($timeSlotsByDate as $date => $timeSlots) {
            $carbonDate = Carbon::parse($date);
            $formattedTimeSlots[$date] = [
                'formatted_date' => $carbonDate->translatedFormat('l j F Y'),
                'time_slots' => $timeSlots
            ];
        }

        return view('issuer.schedule', [
            'timeSlotsByDate' => $formattedTimeSlots
        ]);
    }

    /**
     * Update the availability of a time slot
     */
    public function update(Request $request, TimeSlot $timeSlot)
    {
        $user = Auth::user();

        // Check if the time slot belongs to the authenticated issuer
        if ($timeSlot->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot does not belong to you.'
            ], 403);
        }

        // Check if the time slot has scheduled meetings if it is being marked as unavailable
        if (!$request->boolean('availability') && $timeSlot->meetings->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot has scheduled meetings and cannot be marked as unavailable.'
            ], 400);
        }

        // Update availability
        try {
            $timeSlot->update([
                'availability' => $request->boolean('availability')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully.',
                'availability' => $timeSlot->availability
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update all availabilities of the issuer for a given date
     */
    public function updateByDate(Request $request, $date)
    {
        $user = Auth::user();
        $availability = $request->boolean('availability');

        try {
            DB::beginTransaction();

            // If marking all time slots as unavailable, check that none have meetings
            if (!$availability) {
                $bookedTimeSlots = $user->timeSlots()
                    ->whereDate('date', $date)
                    ->whereHas('meetings')
                    ->count();

                if ($bookedTimeSlots > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some time slots have scheduled meetings and cannot be marked as unavailable.'
                    ], 400);
                }
            }

            // Update all time slots for the specified date
            $user->timeSlots()
                ->whereDate('date', $date)
                ->update(['availability' => $availability]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'All availabilities have been updated successfully.',
                'availability' => $availability
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update the availabilities of time slots for a specific date
     */
    public function batchUpdate(Request $request, $date)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'timeslots' => 'required|array',
            'timeslots.*.id' => 'required|integer|exists:time_slots,id',
            'timeslots.*.availability' => 'required|boolean',
        ]);

        $timeslots = $request->input('timeslots');
        $timeslotIds = array_column($timeslots, 'id');

        try {
            DB::beginTransaction();

            // Retrieve all relevant time slots
            $userTimeSlots = TimeSlot::whereIn('id', $timeslotIds)
                ->where('user_id', $user->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('id');

            // Check that all time slots belong to the authenticated issuer
            if (count($userTimeSlots) !== count($timeslotIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more time slots do not belong to you.'
                ], 403);
            }

            // Check if any time slots with meetings are being marked as unavailable
            foreach ($timeslots as $timeslot) {
                $timeSlot = $userTimeSlots[$timeslot['id']] ?? null;

                if ($timeSlot && !$timeslot['availability'] && $timeSlot->meetings->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more time slots have scheduled meetings and cannot be marked as unavailable.'
                    ], 400);
                }
            }

            // All validations passed, proceed with the update
            foreach ($timeslots as $timeslot) {
                $timeSlot = $userTimeSlots[$timeslot['id']];
                $timeSlot->availability = $timeslot['availability'];
                $timeSlot->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'All availabilities have been updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate time slots for the authenticated issuer user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateTimeSlots()
    {
        $user = auth()->user();

        try {
            // Check if the user already has time slots with meetings
            if ($user->timeSlots()->whereHas('meetings')->exists()) {
                return redirect()->back()->with('error', 'Cannot regenerate time slots because you have time slots with scheduled meetings.');
            }

            // Use the command we created earlier with --force option to regenerate slots
            $output = Artisan::call('issuers:generate-timeslots', [
                '--user-id' => $user->id,
                '--force' => true
            ]);

            // Get command output to display appropriate message
            $commandOutput = Artisan::output();

            if (strpos($commandOutput, 'already has time slots') !== false) {
                return redirect()->back()->with('info', 'You already have time slots. No changes made.');
            }

            return redirect()->back()->with('success', 'Time slots have been generated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to generate time slots: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate time slots: ' . $e->getMessage());
        }
    }
}
