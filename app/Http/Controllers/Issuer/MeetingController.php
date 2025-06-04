<?php

namespace App\Http\Controllers\Issuer;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Enums\InvestorStatus;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingInvestor;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Display a listing of the issuer's meetings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = Meeting::query();

        // Only show meetings for this issuer
        $query->where('issuer_id', $user->id);
        $query->with(['room', 'timeSlot', 'investors', 'questions']);

        // Filter by date
        $date = $request->input('date');
        if ($date && $date !== 'all') {
            $query->whereHas('timeSlot', function($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        // Filter by status
        $status = $request->input('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by format (one-on-one or group)
        $format = $request->input('format');
        if ($format !== null && $format !== 'all') {
            $query->where('is_one_on_one', (bool) $format);
        }

        // Search by notes, investor name, etc.
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('room', function($rq) use ($search) {
                      $rq->where('name', 'like', "%{$search}%")
                         ->orWhere('location', 'like', "%{$search}%");
                  })
                  ->orWhereHas('investors', function ($iq) use ($search) {
                      $iq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhereHas('organization', function ($oq) use ($search) {
                             $oq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        // Sort by date and time
        $query = $query->join('time_slots', 'meetings.time_slot_id', '=', 'time_slots.id')
              ->orderBy('time_slots.date', 'asc')
              ->orderBy('time_slots.start_time', 'asc')
              ->select('meetings.*');

        // Apply withCount after the join and select
        $query->withCount(['investors', 'questions']);

        $meetings = $query->paginate(10)->withQueryString();

        // Load data for filters
        $dates = DB::table('time_slots')
                  ->join('meetings', 'time_slots.id', '=', 'meetings.time_slot_id')
                  ->where('meetings.issuer_id', $user->id)
                  ->select(DB::raw('DATE(time_slots.date) as date'))
                  ->distinct()
                  ->orderBy('date')
                  ->pluck('date')
                  ->toArray();

        return view('issuer.meetings.index', compact('meetings', 'dates'));
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Check if the meeting belongs to this issuer
        if ($meeting->issuer_id !== $user->id) {
            return redirect()->route('issuer.meetings.index')
                ->with('error', 'You do not have permission to view this meeting.');
        }

        // Eager load relationships
        $meeting->load(['room', 'timeSlot', 'investors', 'questions', 'questions.investor']);

        return view('issuer.meetings.show', compact('meeting'));
    }

    /**
     * Update the status of the meeting.
     */
    public function updateStatus(Request $request, Meeting $meeting)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Check if the meeting belongs to this issuer
        if ($meeting->issuer_id !== $user->id) {
            return redirect()->route('issuer.meetings.index')
                ->with('error', 'You do not have permission to update this meeting.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(fn($status) => $status->value, MeetingStatus::cases())),
        ]);

        try {
            $meeting->status = $validated['status'];
            $meeting->save();

            $statusLabel = MeetingStatus::from($validated['status'])->label();

            return redirect()->route('issuer.meetings.show', $meeting)
                ->with('success', "Meeting status updated to {$statusLabel}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update meeting status: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of an investor in a meeting.
     */
    public function updateInvestorStatus(Request $request, Meeting $meeting, $investorId)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Check if the meeting belongs to this issuer
        if ($meeting->issuer_id !== $user->id) {
            return redirect()->route('issuer.meetings.index')
                ->with('error', 'You do not have permission to update this meeting.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_map(fn($status) => $status->value, InvestorStatus::cases())),
        ]);

        try {
            $meetingInvestor = MeetingInvestor::where('meeting_id', $meeting->id)
                                          ->where('investor_id', $investorId)
                                          ->firstOrFail();

            $meetingInvestor->status = $validated['status'];
            $meetingInvestor->save();

            $statusLabel = InvestorStatus::from($validated['status'])->label();

            return redirect()->route('issuer.meetings.show', $meeting)
                ->with('success', "Investor status updated to {$statusLabel}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update investor status: ' . $e->getMessage());
        }
    }
}
