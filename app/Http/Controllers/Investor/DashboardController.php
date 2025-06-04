<?php

namespace App\Http\Controllers\Investor;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the investor dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get all meetings for this investor
        $meetings = $user->investorMeetings()
            ->with(['room', 'timeSlot', 'issuer.organization', 'meetingInvestors' => function($query) use ($user) {
                $query->where('investor_id', $user->id);
            }])
            ->get();

        // Calculate meeting statistics
        $upcomingMeetings = $meetings->filter(function($meeting) {
            return $meeting->timeSlot && $meeting->timeSlot->start_time > now();
        });

        $pendingMeetings = $meetings->filter(function($meeting) use ($user) {
            $meetingInvestor = $meeting->meetingInvestors->where('investor_id', $user->id)->first();
            return $meetingInvestor && $meetingInvestor->status === \App\Enums\InvestorStatus::PENDING;
        });

        $confirmedMeetings = $meetings->filter(function($meeting) use ($user) {
            $meetingInvestor = $meeting->meetingInvestors->where('investor_id', $user->id)->first();
            return $meetingInvestor && $meetingInvestor->status === \App\Enums\InvestorStatus::CONFIRMED;
        });

        $completedMeetings = $meetings->filter(function($meeting) {
            return $meeting->timeSlot && $meeting->timeSlot->end_time < now();
        });

        $attendedMeetings = $meetings->filter(function($meeting) use ($user) {
            $meetingInvestor = $meeting->meetingInvestors->where('investor_id', $user->id)->first();
            return $meetingInvestor && $meetingInvestor->status === \App\Enums\InvestorStatus::ATTENDED;
        });

        $cancelledMeetings = $meetings->filter(function($meeting) use ($user) {
            $meetingInvestor = $meeting->meetingInvestors->where('investor_id', $user->id)->first();
            return $meetingInvestor && $meetingInvestor->status === \App\Enums\InvestorStatus::REFUSED;
        });

        // Questions statistics
        $totalQuestions = $user->questions()->count();
        $answeredQuestions = $user->questions()->whereNotNull('response')->count();
        $unansweredQuestions = $totalQuestions - $answeredQuestions;

        // Issuer interaction statistics
        $issuersMetWith = $meetings->pluck('issuer')->unique('id')->count();
        $totalIssuers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', \App\Enums\UserRole::ISSUER->value);
        })->where('status', true)->count();

        // Organization statistics
        $organizationsMetWith = $meetings->pluck('issuer.organization')->unique('id')->filter()->count();

        // Upcoming meetings for the "Next Meetings" section (limit to 5)
        $upcomingMeetingsForDisplay = $upcomingMeetings->sortBy('timeSlot.start_time')->take(5);

        // Meeting effectiveness ratio
        $meetingEffectiveness = $completedMeetings->count() > 0 ?
            round(($attendedMeetings->count() / $completedMeetings->count()) * 100, 1) : 0;

        return view('investor.dashboard', compact(
            'upcomingMeetings',
            'pendingMeetings',
            'confirmedMeetings',
            'completedMeetings',
            'attendedMeetings',
            'cancelledMeetings',
            'totalQuestions',
            'answeredQuestions',
            'unansweredQuestions',
            'issuersMetWith',
            'totalIssuers',
            'organizationsMetWith',
            'upcomingMeetingsForDisplay',
            'meetingEffectiveness'
        ));
    }

    /**
     * Display the investor's calendar view.
     */
    public function calendar()
    {
        $user = Auth::user();

        $meetings = $user->investorMeetings()
            ->with(['room', 'timeSlot', 'issuer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format meetings for calendar display
        $calendarEvents = $meetings->map(function ($meeting) {
            $timeSlot = $meeting->timeSlot;
            $issuer = $meeting->issuer->name;

            return [
                'id' => $meeting->id,
                'title' => "Meeting with $issuer",
                'start' => $timeSlot->start_time->format('Y-m-d\TH:i:s'),
                'end' => $timeSlot->end_time->format('Y-m-d\TH:i:s'),
                'url' => route('investor.meetings.show', $meeting->id),
                'status' => $meeting->status->value
            ];
        });

        return view('investor.calendar', compact('calendarEvents'));
    }

    /**
     * Display the investor's statistics.
     */
    public function statistics()
    {
        $user = Auth::user();

        $meetings = $user->investorMeetings()->get();

        // Get count for different meeting statuses
        $totalMeetings = $meetings->count();
        $scheduledMeetings = $meetings->where('status', MeetingStatus::SCHEDULED)->count();
        $completedMeetings = $meetings->where('status', MeetingStatus::COMPLETED)->count();
        $cancelledMeetings = $meetings->where('status', MeetingStatus::CANCELLED)->count();

        // Get questions statistics
        $totalQuestions = $user->questions()->count();
        $answeredQuestions = $user->questions()->where('is_answered', true)->count();
        $unansweredQuestions = $totalQuestions - $answeredQuestions;

        return view('investor.statistics', compact(
            'totalMeetings',
            'scheduledMeetings',
            'completedMeetings',
            'cancelledMeetings',
            'totalQuestions',
            'answeredQuestions',
            'unansweredQuestions'
        ));
    }
}
