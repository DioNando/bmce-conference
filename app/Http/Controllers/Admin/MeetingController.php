<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\MeetingInvitation;
use App\Models\Meeting;
use App\Models\MeetingInvestor;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class MeetingController extends Controller
{
    /**
     * Display a listing of the meetings.
     */
    public function index(Request $request)
    {
        $query = Meeting::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('issuer', function ($subq) use ($search) {
                        $subq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhereHas('organization', function ($orgq) use ($search) {
                                $orgq->where('name', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('room', function ($roomq) use ($search) {
                        $roomq->where('name', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by date
        if ($request->filled('date') && $request->input('date') !== 'all') {
            $date = $request->input('date');
            $query->whereHas('timeSlot', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        // Filter by issuer
        if ($request->filled('issuer_id') && $request->input('issuer_id') !== 'all') {
            $query->where('issuer_id', $request->input('issuer_id'));
        }

        // Filter by room
        if ($request->filled('room_id')) {
            if ($request->input('room_id') === 'null') {
                $query->whereNull('room_id');
            } elseif ($request->input('room_id') !== 'all') {
                $query->where('room_id', $request->input('room_id'));
            }
        }

        // Filter by format (one-on-one or group)
        if ($request->filled('format') && $request->input('format') !== 'all') {
            $query->where('is_one_on_one', (bool) $request->input('format'));
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Join with time_slots table for ordering
        $query = $query->join('time_slots', 'meetings.time_slot_id', '=', 'time_slots.id');

        // Handle sorting
        $sortBy = $request->input('sort_by', 'time_slots.date');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort parameters
        $validSortFields = ['time_slots.date', 'time_slots.start_time', 'created_at'];
        $validSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'time_slots.date';
        }

        if (!in_array($sortOrder, $validSortOrders)) {
            $sortOrder = 'desc';
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Add secondary sort for consistency when primary sort field has duplicates
        if ($sortBy !== 'time_slots.start_time') {
            $query->orderBy('time_slots.start_time', 'asc');
        }

        // Select all fields from meetings to avoid column conflicts
        $query->select('meetings.*');

        // Paginate results
        $perPage = $request->input('perPage', 10);
        // Load relationships and counts just before pagination
        $meetings = $query->with(['room', 'timeSlot', 'issuer', 'issuer.organization', 'investors'])
            ->withCount(['investors', 'questions'])
            ->paginate($perPage)->withQueryString();

        // Load data for filters
        $rooms = Room::orderBy('name')->get();
        $issuers = User::with('organization')->role('issuer')->whereNotNull('organization_id')->orderBy('name')->get();
        $dates = DB::table('time_slots')
            ->join('meetings', 'time_slots.id', '=', 'meetings.time_slot_id')
            ->select(DB::raw('DATE(time_slots.date) as date'))
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        return view('admin.meetings.index', compact('meetings', 'rooms', 'issuers', 'dates', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create()
    {
        $user = Auth::user();

        $rooms = Room::all();

        // Récupérer seulement les créneaux disponibles sans réunion
        $timeSlots = TimeSlot::where('availability', true)
            ->whereDoesntHave('meetings', function ($query) {
                $query->where('status', '!=', MeetingStatus::CANCELLED->value);
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $issuers = User::role(UserRole::ISSUER->value)->whereNotNull('organization_id')->get();
        $investors = User::role(UserRole::INVESTOR->value)->get();

        return view('admin.meetings.create', compact('rooms', 'timeSlots', 'issuers', 'investors'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'issuer_id' => 'required|exists:users,id',
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
            'status' => 'nullable|string|in:' . implode(',', array_map(fn($status) => $status->value, MeetingStatus::all())),
            'notes' => 'nullable|string',
            'is_one_on_one' => 'boolean',
        ]);

        // Vérifier qu'il n'y a pas déjà une réunion pour cet émetteur sur ce créneau
        $existingMeeting = Meeting::where('time_slot_id', $validated['time_slot_id'])
            ->where('issuer_id', $validated['issuer_id'])
            ->where('status', '!=', MeetingStatus::CANCELLED->value)
            ->first();

        if ($existingMeeting) {
            return back()->with('error', 'This issuer already has a meeting scheduled for this time slot.')
                ->withInput();
        }

        // Vérifier qu'aucun investisseur n'a déjà une participation confirmée sur ce créneau
        foreach ($validated['investor_ids'] as $investorId) {
            $conflictingMeeting = Meeting::where('time_slot_id', $validated['time_slot_id'])
                ->where('status', '!=', MeetingStatus::CANCELLED->value)
                ->whereHas('meetingInvestors', function ($query) use ($investorId) {
                    $query->where('investor_id', $investorId)
                          ->where('status', \App\Enums\InvestorStatus::CONFIRMED->value);
                })
                ->with('issuer')
                ->first();

            if ($conflictingMeeting) {
                $investor = User::find($investorId);
                return back()->with('error', "Investor {$investor->name} {$investor->first_name} already has a confirmed participation with {$conflictingMeeting->issuer->name} {$conflictingMeeting->issuer->first_name} on this time slot.")
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $meeting = Meeting::create([
                'room_id' => $validated['room_id'] ?? null,
                'time_slot_id' => $validated['time_slot_id'],
                'issuer_id' => $validated['issuer_id'],
                'created_by_id' => $user->id,
                'updated_by_id' => $user->id,
                'status' => $validated['status'] ?? MeetingStatus::SCHEDULED->value,
                'notes' => $validated['notes'] ?? null,
                'is_one_on_one' => $validated['is_one_on_one'] ?? false,
            ]);

            // Associate investors
            foreach ($validated['investor_ids'] as $investorId) {
                $meeting->meetingInvestors()->create([
                    'investor_id' => $investorId,
                    'status' => MeetingStatus::CONFIRMED->value,
                ]);
            }

            // Rechargez les relations pour avoir les investisseurs associés
            $meeting->load('investors');

            // Déclenchez manuellement les notifications
            app(NotificationService::class)->notifyMeetingInvestors(
                $meeting,
                'Invitation à un meeting',
                "Vous êtes invité à un meeting avec {$meeting->issuer->organization->name} le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
            );

            DB::commit();

            return redirect()->route('admin.meetings.show', $meeting)
                ->with('success', 'Meeting created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create meeting: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting)
    {
        $user = Auth::user();



        $meeting->load(['room', 'timeSlot', 'issuer', 'investors', 'questions', 'createdBy', 'updatedBy']);

        return view('admin.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Meeting $meeting)
    {
        $user = Auth::user();

        $meeting->load(['issuer', 'investors']);

        $rooms = Room::all();

        // Récupérer les créneaux disponibles sans réunion + le créneau actuel de cette réunion
        $timeSlots = TimeSlot::where('availability', true)
            ->where(function ($query) use ($meeting) {
                $query->whereDoesntHave('meetings', function ($subQuery) {
                    $subQuery->where('status', '!=', MeetingStatus::CANCELLED->value);
                })
                ->orWhere('id', $meeting->time_slot_id);
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $issuers = User::role(UserRole::ISSUER->value)->whereNotNull('organization_id')->get();
        $investors = User::role(UserRole::INVESTOR->value)->get();

        return view('admin.meetings.edit', compact('meeting', 'rooms', 'timeSlots', 'issuers', 'investors'));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $user = Auth::user();



        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'issuer_id' => 'required|exists:users,id',
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:' . implode(',', array_map(fn($status) => $status->value, MeetingStatus::all())),
            'is_one_on_one' => 'boolean',
        ]);

        // Vérifier qu'il n'y a pas déjà une réunion pour cet émetteur sur ce créneau (sauf la réunion actuelle)
        $existingMeeting = Meeting::where('time_slot_id', $validated['time_slot_id'])
            ->where('issuer_id', $validated['issuer_id'])
            ->where('status', '!=', MeetingStatus::CANCELLED->value)
            ->where('id', '!=', $meeting->id)
            ->first();

        if ($existingMeeting) {
            return back()->with('error', 'This issuer already has a meeting scheduled for this time slot.')
                ->withInput();
        }

        // Vérifier qu'aucun investisseur n'a déjà une participation confirmée sur ce créneau (sauf la réunion actuelle)
        foreach ($validated['investor_ids'] as $investorId) {
            $conflictingMeeting = Meeting::where('time_slot_id', $validated['time_slot_id'])
                ->where('status', '!=', MeetingStatus::CANCELLED->value)
                ->where('id', '!=', $meeting->id)
                ->whereHas('meetingInvestors', function ($query) use ($investorId) {
                    $query->where('investor_id', $investorId)
                          ->where('status', \App\Enums\InvestorStatus::CONFIRMED->value);
                })
                ->with('issuer')
                ->first();

            if ($conflictingMeeting) {
                $investor = User::find($investorId);
                return back()->with('error', "Investor {$investor->name} {$investor->first_name} already has a confirmed participation with {$conflictingMeeting->issuer->name} {$conflictingMeeting->issuer->first_name} on this time slot.")
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $meeting->update([
                'room_id' => $validated['room_id'] ?? null,
                'time_slot_id' => $validated['time_slot_id'],
                'issuer_id' => $validated['issuer_id'],
                'updated_by_id' => $user->id,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $meeting->notes,
                'is_one_on_one' => $validated['is_one_on_one'] ?? $meeting->is_one_on_one,
            ]);

            // Update investors
            $meeting->meetingInvestors()->delete();

            foreach ($validated['investor_ids'] as $investorId) {
                $meeting->meetingInvestors()->create([
                    'investor_id' => $investorId,
                    'status' => MeetingStatus::CONFIRMED->value,
                ]);
            }

            // // Rechargez les relations pour avoir les investisseurs associés
            // $meeting->load(['investors', 'issuer', 'timeSlot']);

            // // Déclenchez manuellement les notifications de mise à jour
            // app(NotificationService::class)->notifyMeetingInvestors(
            //     $meeting,
            //     'Mise à jour de meeting',
            //     "Le meeting avec {$meeting->issuer->organization->name} a été mis à jour. Il est maintenant prévu le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}."
            // );

            DB::commit();

            return redirect()->route('admin.meetings.show', $meeting)
                ->with('success', 'Réunion mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Échec de la mise à jour de la réunion. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy(Meeting $meeting)
    {
        try {
            // // Chargez les relations avant la suppression pour pouvoir notifier
            // $meeting->load(['investors', 'issuer', 'timeSlot']);

            // // Créez un message de notification pour les investisseurs
            // $notificationMessage = "Le meeting avec {$meeting->issuer->organization->name} prévu le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')} a été annulé.";

            // // Envoyez les notifications manuellement avant de supprimer
            // app(NotificationService::class)->notifyMeetingInvestors(
            //     $meeting,
            //     'Annulation de meeting',
            //     $notificationMessage
            // );

            // // Supprimez la réunion
            $meeting->delete();

            return redirect()->route('admin.meetings.index')
                ->with('success', 'Réunion supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Échec de la suppression de la réunion. ' . $e->getMessage());
        }
    }

    /**
     * Export meetings to Excel/CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();



        // Récupérer les filtres actuels
        $date = $request->input('date', 'all');
        $issuerId = $request->input('issuer_id');
        $roomId = $request->input('room_id');
        $format = $request->input('format');
        $status = $request->input('status');
        $search = $request->input('search');

        // Générer un nom de fichier dynamique
        $fileName = 'meetings_' . now()->format('Y-m-d_His') . '.xlsx';

        // Exporter les données
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MeetingsExport($date, $issuerId, $roomId, $format, $search, $status),
            $fileName
        );
    }

    /**
     * Update the status of an investor in a meeting.
     */
    public function updateInvestorStatus(Request $request, Meeting $meeting, $investorId)
    {
        $user = Auth::user();



        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(fn($status) => $status->value, \App\Enums\InvestorStatus::all())),
        ]);

        try {
            $meetingInvestor = MeetingInvestor::where('meeting_id', $meeting->id)
                ->where('investor_id', $investorId)
                ->firstOrFail();

            $meetingInvestor->status = $validated['status'];
            $meetingInvestor->save();

            // return redirect()->route('admin.meetings.show', $meeting)
            //               ->with('success', 'Statut de l\'investisseur mis à jour avec succès.');
            return back()->with('success', 'Statut de l\'investisseur mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Échec de la mise à jour du statut. ' . $e->getMessage());
        }
    }

    /**
     * Display the investors for a specific meeting.
     */
    public function investors(Meeting $meeting)
    {
        $user = Auth::user();



        $meeting->load([
            'issuer',
            'investors',
            'investors.organization',
            'meetingInvestors',
            'timeSlot',
            'room'
        ]);

        return view('admin.meetings.investors', compact('meeting'));
    }

    /**
     * Update the status of multiple investors in a meeting.
     */
    public function updateMultipleInvestorsStatus(Request $request, Meeting $meeting)
    {
        $user = Auth::user();



        $validated = $request->validate([
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
            'status' => 'required|string|in:' . implode(',', array_map(fn($status) => $status->value, \App\Enums\InvestorStatus::all())),
        ]);

        $count = 0;

        foreach ($validated['investor_ids'] as $investorId) {
            try {
                $meetingInvestor = MeetingInvestor::where('meeting_id', $meeting->id)
                    ->where('investor_id', $investorId)
                    ->first();

                if ($meetingInvestor) {
                    $meetingInvestor->status = $validated['status'];
                    $meetingInvestor->save();
                    $count++;
                }
            } catch (\Exception $e) {
                // Continue to the next investor if there's an error
                continue;
            }
        }

        return redirect()->back()->with('success', $count . ' investisseurs mis à jour avec succès.');
    }

    /**
     * Send invitation email to an investor.
     */
    public function sendInvitationEmail(Meeting $meeting, $investorId)
    {
        try {
            $meetingInvestor = MeetingInvestor::where('meeting_id', $meeting->id)
                ->where('investor_id', $investorId)
                ->firstOrFail();

            // Charger explicitement toutes les relations nécessaires pour éviter les erreurs
            $meetingInvestor->load([
                'investor',
                'meeting.issuer.organization',
                'meeting.timeSlot',
                'meeting.room'
            ]);

            Mail::to($meetingInvestor->investor->email)->send(new MeetingInvitation($meetingInvestor));

            // Update invitation sent status
            $meetingInvestor->invitation_sent = true;
            $meetingInvestor->invitation_sent_at = now();
            $meetingInvestor->save();

            return back()->with('success', 'Email d\'invitation envoyé avec succès.');
        } catch (\Exception $e) {
            // Enregistrer l'erreur complète dans les logs
            Log::error('Erreur d\'envoi d\'email: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Échec de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Send invitation emails to multiple investors.
     */
    public function sendMultipleInvitationEmails(Request $request, Meeting $meeting)
    {
        $user = Auth::user();



        $validated = $request->validate([
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
        ]);

        $count = 0;
        $failedCount = 0;

        foreach ($validated['investor_ids'] as $investorId) {
            try {
                $meetingInvestor = MeetingInvestor::where('meeting_id', $meeting->id)
                    ->where('investor_id', $investorId)
                    ->first();

                if ($meetingInvestor) {
                    // Charger explicitement toutes les relations nécessaires
                    $meetingInvestor->load([
                        'investor',
                        'meeting.issuer.organization',
                        'meeting.timeSlot',
                        'meeting.room'
                    ]);

                    Mail::to($meetingInvestor->investor->email)->send(new MeetingInvitation($meetingInvestor));

                    // Update invitation sent status
                    $meetingInvestor->invitation_sent = true;
                    $meetingInvestor->invitation_sent_at = now();
                    $meetingInvestor->save();

                    $count++;
                }
            } catch (\Exception $e) {
                // Log l'erreur pour le debugging
                Log::error('Erreur d\'envoi d\'email: ' . $e->getMessage(), [
                    'investorId' => $investorId,
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);

                $failedCount++;
                // Continue to the next investor if there's an error
                continue;
            }
        }

        $message = $count . ' email(s) d\'invitation envoyé(s) avec succès.';
        if ($failedCount > 0) {
            $message .= ' ' . $failedCount . ' envoi(s) ont échoué.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export meeting with investors list to PDF.
     */
    public function exportInvestorsPdf(Meeting $meeting)
    {
        $user = Auth::user();



        $exporter = new \App\Exports\MeetingInvestorsExport($meeting);
        return $exporter->download();
    }

    /**
     * Export QR code to PDF for a specific investor in a meeting.
     */
    public function exportQrCodePdf(Meeting $meeting, $investorId)
    {
        $user = Auth::user();



        $meetingInvestor = MeetingInvestor::findOrFail($investorId);

        if ($meetingInvestor->meeting_id != $meeting->id) {
            return redirect()->route('admin.meetings.investors', $meeting)
                ->with('error', 'This investor is not associated with the specified meeting.');
        }

        $exporter = new \App\Exports\QrCodeExport($meeting, $meetingInvestor);
        return $exporter->download();
    }
}
