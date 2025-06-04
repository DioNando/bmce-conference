<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IssuerScheduleController extends Controller
{
    /**
     * Affiche la vue de gestion des disponibilités de l'émetteur
     */
    public function show(Request $request, User $user)
    {
        // Vérifier que l'utilisateur est bien un émetteur
        if (!$user->isIssuer()) {
            return redirect()->back()->with('error', 'This user is not an issuer.');
        }

        // Vérifier s'il y a un message d'erreur dans les paramètres d'URL
        if ($request->has('error')) {
            return redirect()->route('admin.users.schedule', $user)->with('error', $request->query('error'));
        }

        // Vérifier s'il y a un message de succès dans les paramètres d'URL
        if ($request->has('success')) {
            return redirect()->route('admin.users.schedule', $user)->with('success', $request->query('success'));
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

        return view('admin.issuer-schedule', [
            'user' => $user,
            'timeSlotsByDate' => $formattedTimeSlots
        ]);
    }

    /**
     * Met à jour la disponibilité d'un créneau horaire
     */
    public function update(Request $request, User $user, TimeSlot $timeSlot)
    {
        // Vérifier que le créneau appartient bien à l'émetteur
        if ($timeSlot->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot does not belong to this issuer.'
            ], 403);
        }

        // Mettre à jour la disponibilité
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
     * Met à jour toutes les disponibilités d'un émetteur pour une date donnée
     */
    public function updateByDate(Request $request, User $user, $date)
    {
        $availability = $request->boolean('availability');

        try {
            DB::beginTransaction();

            // Mettre à jour tous les créneaux de la date spécifiée
            $user->timeSlots()
                ->whereDate('date', $date)
                ->update(['availability' => $availability]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'All availabilities have been updated.',
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
     * Met à jour par lot les disponibilités des créneaux horaires pour une date spécifique
     */
    public function batchUpdate(Request $request, User $user, $date)
    {
        // Valider la requête
        $request->validate([
            'timeslots' => 'required|array',
            'timeslots.*.id' => 'required|integer|exists:time_slots,id',
            'timeslots.*.availability' => 'required|boolean',
        ]);

        $timeslots = $request->input('timeslots');
        $timeslotIds = array_column($timeslots, 'id');

        try {
            DB::beginTransaction();

            // Récupérer tous les créneaux concernés
            $userTimeSlots = TimeSlot::whereIn('id', $timeslotIds)
                ->where('user_id', $user->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('id');

            // Vérifier que tous les créneaux appartiennent à l'utilisateur
            if (count($userTimeSlots) !== count($timeslotIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more time slots do not belong to this issuer.'
                ], 403);
            }

            // Vérifier si des créneaux avec des réunions sont passés à indisponible
            foreach ($timeslots as $timeslot) {
                $timeSlot = $userTimeSlots[$timeslot['id']] ?? null;

                if ($timeSlot && !$timeslot['availability'] && $timeSlot->meetings->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more time slots have scheduled meetings and cannot be marked as unavailable.'
                    ], 400);
                }
            }

            // Tout est validé, procéder à la mise à jour
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
}
