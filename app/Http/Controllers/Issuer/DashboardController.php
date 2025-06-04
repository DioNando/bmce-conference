<?php

namespace App\Http\Controllers\Issuer;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the issuer dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get all meetings for this issuer
        $meetings = Meeting::where('issuer_id', $user->id)
            ->with(['investors', 'timeSlot', 'room', 'meetingInvestors'])
            ->get();

        // Calculate meeting statistics by status
        $upcomingMeetings = $meetings->filter(function($meeting) {
            return $meeting->timeSlot && $meeting->timeSlot->start_time > now();
        });

        $scheduledMeetings = $meetings->where('status', \App\Enums\MeetingStatus::SCHEDULED);
        $completedMeetings = $meetings->where('status', \App\Enums\MeetingStatus::COMPLETED);
        $cancelledMeetings = $meetings->where('status', \App\Enums\MeetingStatus::CANCELLED);
        $pendingMeetings = $meetings->where('status', \App\Enums\MeetingStatus::PENDING);

        // Calculate investor interaction statistics
        $totalInvestorsMet = $meetings->flatMap->investors->unique('id')->count();
        $totalActiveInvestors = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', \App\Enums\UserRole::INVESTOR->value);
        })->where('status', true)->count();

        // Organization coverage statistics
        $organizationsCovered = $meetings->flatMap->investors
            ->whereNotNull('organization_id')
            ->pluck('organization_id')
            ->unique()
            ->count();

        // Questions received statistics - Get questions from meetings this issuer hosts
        $totalQuestions = \App\Models\Question::whereHas('meeting', function($query) use ($user) {
            $query->where('issuer_id', $user->id);
        })->count();
        $answeredQuestions = \App\Models\Question::whereHas('meeting', function($query) use ($user) {
            $query->where('issuer_id', $user->id);
        })->where('is_answered', true)->count();
        $unansweredQuestions = $totalQuestions - $answeredQuestions;

        // Time slot availability statistics
        $totalTimeSlots = \App\Models\TimeSlot::where('user_id', $user->id)->count();
        $availableTimeSlots = \App\Models\TimeSlot::where('user_id', $user->id)
            ->where('availability', true)->count();
        $bookedTimeSlots = \App\Models\TimeSlot::where('user_id', $user->id)
            ->where('availability', false)->count();

        // Meeting effectiveness metrics
        $meetingAttendanceRate = $completedMeetings->count() > 0 ?
            round(($completedMeetings->count() / ($completedMeetings->count() + $cancelledMeetings->count())) * 100, 1) : 0;

        // Response rate for questions
        $questionResponseRate = $totalQuestions > 0 ?
            round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        // Get upcoming meetings for display (limit to 5)
        $upcomingMeetingsForDisplay = $upcomingMeetings->sortBy('timeSlot.start_time')->take(5);

        // Calculate monthly meeting trends (last 6 months)
        $monthlyMeetings = [];
        for($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $meetings->filter(function($meeting) use ($month) {
                return $meeting->created_at->isSameMonth($month);
            })->count();
            $monthlyMeetings[$month->format('M Y')] = $count;
        }

        return view('issuer.dashboard', compact(
            'upcomingMeetings',
            'scheduledMeetings',
            'completedMeetings',
            'cancelledMeetings',
            'pendingMeetings',
            'totalInvestorsMet',
            'totalActiveInvestors',
            'organizationsCovered',
            'totalQuestions',
            'answeredQuestions',
            'unansweredQuestions',
            'totalTimeSlots',
            'availableTimeSlots',
            'bookedTimeSlots',
            'meetingAttendanceRate',
            'questionResponseRate',
            'upcomingMeetingsForDisplay',
            'monthlyMeetings'
        ));
    }

    /**
     * Display the issuer's calendar view.
     */
    public function calendar()
    {
        $user = Auth::user();

        $meetings = Meeting::where('issuer_id', $user->id)
            ->with(['room', 'timeSlot', 'investors'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format meetings for calendar display
        $calendarEvents = $meetings->map(function ($meeting) {
            $timeSlot = $meeting->timeSlot;
            $investors = $meeting->investors->pluck('name')->implode(', ');

            return [
                'id' => $meeting->id,
                'title' => "Meeting with $investors",
                'start' => $timeSlot->start_time->format('Y-m-d\TH:i:s'),
                'end' => $timeSlot->end_time->format('Y-m-d\TH:i:s'),
                'url' => route('issuer.meetings.show', $meeting->id),
                'status' => $meeting->status->value
            ];
        });

        return view('issuer.calendar', compact('calendarEvents'));
    }

    /**
     * Display the issuer's statistics.
     */
    public function statistics()
    {
        $user = Auth::user();

        $totalMeetings = Meeting::where('issuer_id', $user->id)->count();
        $scheduledMeetings = Meeting::where('issuer_id', $user->id)
            ->where('status', MeetingStatus::SCHEDULED)
            ->count();
        $completedMeetings = Meeting::where('issuer_id', $user->id)
            ->where('status', MeetingStatus::COMPLETED)
            ->count();
        $cancelledMeetings = Meeting::where('issuer_id', $user->id)
            ->where('status', MeetingStatus::CANCELLED)
            ->count();

        // Get meetings grouped by month for chart
        $meetingsByMonth = Meeting::where('issuer_id', $user->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return view('issuer.statistics', compact(
            'totalMeetings',
            'scheduledMeetings',
            'completedMeetings',
            'cancelledMeetings',
            'meetingsByMonth'
        ));
    }
}
