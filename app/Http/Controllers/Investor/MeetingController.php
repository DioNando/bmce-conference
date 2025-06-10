<?php

namespace App\Http\Controllers\Investor;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Enums\InvestorStatus;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingInvestor;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Display a listing of the investor's meetings.
     */
    public function index()
    {
        $user = Auth::user();

        $meetings = $user->investorMeetings()
                        ->with(['room', 'timeSlot', 'issuer', 'questions' => function($query) use ($user) {
                            $query->where('investor_id', $user->id);
                        }])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('investor.meetings.index', compact(
            'meetings'
        ));
    }

    /**
     * Display the specified meeting.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('home')
                            ->with('error', 'You do not have permission to access this page.');
        }

        $meeting = Meeting::with(['room', 'timeSlot', 'issuer', 'questions'])
                          ->whereHas('investors', function($query) use ($user) {
                              $query->where('users.id', $user->id);
                          })
                          ->findOrFail($id);

        $questions = $meeting->questions->where('investor_id', $user->id);

        return view('investor.meetings.show', compact('meeting', 'questions'));
    }

    /**
     * Show the form for requesting a meeting with an issuer.
     */
    public function showRequestForm($issuerId)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('home')
                           ->with('error', 'You do not have permission to access this page.');
        }

        // Check if issuer exists
        $issuer = User::whereHas('roles', function($query) {
                        $query->where('name', UserRole::ISSUER->value);
                     })
                     ->where('status', true)
                     ->findOrFail($issuerId);

        // Check if investor already has a meeting with this issuer
        $existingMeeting = Meeting::where('issuer_id', $issuerId)
                               ->whereHas('investors', function ($query) use ($user) {
                                   $query->where('users.id', $user->id);
                               })
                               ->first();

        if ($existingMeeting) {
            return redirect()->route('investor.meetings.show', $existingMeeting->id)
                          ->with('info', 'You already have a meeting scheduled with this issuer.');
        }

        // Get available time slots for this issuer
        $availableTimeSlots = TimeSlot::where('user_id', $issuerId)
                                    ->where('availability', true)
                                    ->orderBy('start_time')
                                    ->get();

        if ($availableTimeSlots->isEmpty()) {
            return redirect()->route('investor.issuers.show', $issuerId)
                           ->with('warning', 'No available time slots for this issuer.');
        }

        // Group time slots by date for better display
        $groupedTimeSlots = $availableTimeSlots->groupBy(function($timeSlot) {
            return $timeSlot->date->format('Y-m-d');
        });

        return view('investor.meetings.request', compact('issuer', 'groupedTimeSlots'));
    }

    /**
     * Request a new meeting with an issuer.
     */
    public function requestMeeting(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('home')
                            ->with('error', 'You do not have permission to access this page.');
        }

        // Validate the input
        $validated = $request->validate([
            'time_slot_id' => 'required|exists:time_slots,id',
            'issuer_id' => 'required|exists:users,id',
            'question' => 'nullable|string|max:1000',
        ]);

        // Check if the time slot is available
        $timeSlot = TimeSlot::where('id', $validated['time_slot_id'])
                           ->where('availability', true)
                           ->first();

        if (!$timeSlot) {
            return redirect()->back()
                           ->with('error', 'The selected time slot is no longer available.');
        }

        // Check if investor already has a meeting with this issuer
        $existingMeeting = Meeting::where('issuer_id', $validated['issuer_id'])
                                ->whereHas('investors', function ($query) use ($user) {
                                    $query->where('users.id', $user->id);
                                })
                                ->first();

        if ($existingMeeting) {
            return redirect()->route('investor.meetings.show', $existingMeeting->id)
                           ->with('info', 'You already have a meeting scheduled with this issuer.');
        }

        DB::beginTransaction();

        try {
            // Check if there is an existing meeting for this time slot and issuer
            $existingMeetingForTimeSlot = Meeting::where('time_slot_id', $validated['time_slot_id'])
                                                ->where('issuer_id', $validated['issuer_id'])
                                                ->where('status', '!=', MeetingStatus::CANCELLED->value)
                                                ->first();

            if ($existingMeetingForTimeSlot) {
                // Add the investor to the existing meeting
                MeetingInvestor::create([
                    'meeting_id' => $existingMeetingForTimeSlot->id,
                    'investor_id' => $user->id,
                    'status' => InvestorStatus::PENDING->value
                ]);

                // Add optional question if provided
                if (!empty($validated['question'])) {
                    $existingMeetingForTimeSlot->questions()->create([
                        'investor_id' => $user->id,
                        'question' => $validated['question'],
                        'is_answered' => false
                    ]);
                }

                $meeting = $existingMeetingForTimeSlot;
            } else {
                // Create a new meeting if no existing one was found
                $meeting = Meeting::create([
                    'room_id' => null,
                    'time_slot_id' => $validated['time_slot_id'],
                    'issuer_id' => $validated['issuer_id'],
                    'created_by_id' => $user->id,
                    'updated_by_id' => $user->id,
                    'status' => MeetingStatus::PENDING->value,
                    'is_one_on_one' => false,
                ]);

                // Associate the investor with the meeting
                MeetingInvestor::create([
                    'meeting_id' => $meeting->id,
                    'investor_id' => $user->id,
                    'status' => InvestorStatus::PENDING->value
                ]);

                // Add optional question if provided
                if (!empty($validated['question'])) {
                    $meeting->questions()->create([
                        'investor_id' => $user->id,
                        'question' => $validated['question'],
                        'is_answered' => false
                    ]);
                }
            }

            // Rechargez les relations nécessaires
            $meeting->load(['timeSlot', 'issuer', 'investors']);

            // Envoyer une notification à l'émetteur concernant la demande de réunion
            app(NotificationService::class)->create(
                $meeting->issuer->id,
                'Nouvelle demande de réunion',
                "L'investisseur {$user->first_name} {$user->name} a demandé une réunion pour le {$meeting->timeSlot->date->format('d/m/Y')} de {$meeting->timeSlot->start_time->format('H:i')} à {$meeting->timeSlot->end_time->format('H:i')}.",
                'meeting',
                $meeting,
                ['meeting_id' => $meeting->id]
            );

            DB::commit();

            return redirect()->route('investor.meetings.show', $meeting->id)
                           ->with('success', 'Meeting request sent successfully. You will be notified once the issuer responds.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Failed to request meeting: ' . $e->getMessage());
        }
    }
}
