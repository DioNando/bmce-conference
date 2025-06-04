<?php

namespace App\Services;

use App\Enums\MeetingStatus;
use App\Enums\UserRole;
use App\Models\Meeting;
use App\Models\Question;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardCacheService
{
    protected const CACHE_TTL = 300; // 5 minutes
    protected const CACHE_PREFIX = 'dashboard_';

    /**
     * Get cached dashboard statistics for issuer
     */
    public function getIssuerDashboardStats(int $userId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'issuer_' . $userId,
            self::CACHE_TTL,
            function () use ($userId) {
                return $this->calculateIssuerStats($userId);
            }
        );
    }

    /**
     * Get cached dashboard statistics for investor
     */
    public function getInvestorDashboardStats(int $userId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'investor_' . $userId,
            self::CACHE_TTL,
            function () use ($userId) {
                return $this->calculateInvestorStats($userId);
            }
        );
    }

    /**
     * Get cached dashboard statistics for admin
     */
    public function getAdminDashboardStats(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'admin_global',
            self::CACHE_TTL,
            function () {
                return $this->calculateAdminStats();
            }
        );
    }

    /**
     * Calculate issuer statistics (heavy computation)
     */
    protected function calculateIssuerStats(int $userId): array
    {
        // Get all meetings with optimized query
        $meetings = Meeting::where('issuer_id', $userId)
            ->with(['investors:id,name,first_name,organization_id', 'timeSlot:id,start_time,end_time', 'room:id,name'])
            ->get();

        // Calculate meeting statistics by status
        $upcomingMeetings = $meetings->filter(function($meeting) {
            return $meeting->timeSlot && $meeting->timeSlot->start_time > now();
        });

        $statusCounts = [
            'scheduled' => $meetings->where('status', MeetingStatus::SCHEDULED)->count(),
            'completed' => $meetings->where('status', MeetingStatus::COMPLETED)->count(),
            'cancelled' => $meetings->where('status', MeetingStatus::CANCELLED)->count(),
            'pending' => $meetings->where('status', MeetingStatus::PENDING)->count(),
        ];

        // Calculate investor interaction statistics
        $totalInvestorsMet = $meetings->flatMap->investors->unique('id')->count();

        // Use optimized query for active investors count
        $totalActiveInvestors = Cache::remember('active_investors_count', 600, function() {
            return User::whereHas('roles', function($query) {
                $query->where('name', UserRole::INVESTOR->value);
            })->where('status', true)->count();
        });

        // Organization coverage statistics
        $organizationsCovered = $meetings->flatMap->investors
            ->whereNotNull('organization_id')
            ->pluck('organization_id')
            ->unique()
            ->count();

        // Questions statistics with optimized queries
        $questionStats = DB::table('questions')
            ->join('meetings', 'questions.meeting_id', '=', 'meetings.id')
            ->where('meetings.issuer_id', $userId)
            ->selectRaw('
                COUNT(*) as total_questions,
                SUM(CASE WHEN questions.is_answered = 1 THEN 1 ELSE 0 END) as answered_questions
            ')
            ->first();

        $totalQuestions = $questionStats->total_questions ?? 0;
        $answeredQuestions = $questionStats->answered_questions ?? 0;
        $unansweredQuestions = $totalQuestions - $answeredQuestions;

        // Time slot statistics with optimized query
        $timeSlotStats = TimeSlot::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_slots,
                SUM(CASE WHEN availability = 1 THEN 1 ELSE 0 END) as available_slots,
                SUM(CASE WHEN availability = 0 THEN 1 ELSE 0 END) as booked_slots
            ')
            ->first();

        // Meeting effectiveness metrics
        $completedCount = $statusCounts['completed'];
        $cancelledCount = $statusCounts['cancelled'];
        $meetingAttendanceRate = ($completedCount + $cancelledCount) > 0 ?
            round(($completedCount / ($completedCount + $cancelledCount)) * 100, 1) : 0;

        // Response rate for questions
        $questionResponseRate = $totalQuestions > 0 ?
            round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        // Get upcoming meetings for display (optimized)
        $upcomingMeetingsForDisplay = $upcomingMeetings
            ->sortBy('timeSlot.start_time')
            ->take(5)
            ->values();

        return [
            'meetings' => [
                'upcoming' => $upcomingMeetings,
                'counts' => $statusCounts,
                'total' => array_sum($statusCounts),
                'upcoming_for_display' => $upcomingMeetingsForDisplay,
            ],
            'investors' => [
                'total_met' => $totalInvestorsMet,
                'total_active' => $totalActiveInvestors,
                'organizations_covered' => $organizationsCovered,
            ],
            'questions' => [
                'total' => $totalQuestions,
                'answered' => $answeredQuestions,
                'unanswered' => $unansweredQuestions,
                'response_rate' => $questionResponseRate,
            ],
            'time_slots' => [
                'total' => $timeSlotStats->total_slots ?? 0,
                'available' => $timeSlotStats->available_slots ?? 0,
                'booked' => $timeSlotStats->booked_slots ?? 0,
            ],
            'metrics' => [
                'attendance_rate' => $meetingAttendanceRate,
                'question_response_rate' => $questionResponseRate,
            ],
        ];
    }

    /**
     * Calculate investor statistics
     */
    protected function calculateInvestorStats(int $userId): array
    {
        // Investor meetings with optimized queries
        $meetingStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_meetings,
                COUNT(CASE WHEN m.status = 'scheduled' THEN 1 END) as scheduled_meetings,
                COUNT(CASE WHEN m.status = 'completed' THEN 1 END) as completed_meetings,
                COUNT(CASE WHEN m.status = 'cancelled' THEN 1 END) as cancelled_meetings,
                COUNT(CASE WHEN m.meeting_datetime > NOW() THEN 1 END) as upcoming_meetings
            FROM meetings m
            WHERE m.investor_id = ?
        ", [$userId]);

        // Questions asked by this investor
        $questionStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_questions,
                COUNT(CASE WHEN q.answer IS NOT NULL AND q.answer != '' THEN 1 END) as answered_questions
            FROM questions q
            WHERE q.investor_id = ?
        ", [$userId]);

        // Issuers met
        $issuersMetCount = DB::selectOne("
            SELECT COUNT(DISTINCT m.issuer_id) as issuers_met
            FROM meetings m
            WHERE m.investor_id = ? AND m.status = 'completed'
        ", [$userId])->issuers_met ?? 0;

        // Recent meetings for display
        $recentMeetings = DB::select("
            SELECT 
                m.id,
                m.meeting_datetime,
                m.status,
                u.first_name as issuer_first_name,
                u.last_name as issuer_last_name,
                u.company_name
            FROM meetings m
            JOIN users u ON m.issuer_id = u.id
            WHERE m.investor_id = ?
            ORDER BY m.meeting_datetime DESC
            LIMIT 5
        ", [$userId]);

        $totalQuestions = $questionStats->total_questions ?? 0;
        $answeredQuestions = $questionStats->answered_questions ?? 0;
        $questionResponseRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        return [
            'meetings' => [
                'total' => $meetingStats->total_meetings ?? 0,
                'scheduled' => $meetingStats->scheduled_meetings ?? 0,
                'completed' => $meetingStats->completed_meetings ?? 0,
                'cancelled' => $meetingStats->cancelled_meetings ?? 0,
                'upcoming' => $meetingStats->upcoming_meetings ?? 0,
                'recent' => $recentMeetings,
            ],
            'issuers' => [
                'total_met' => $issuersMetCount,
            ],
            'questions' => [
                'total' => $totalQuestions,
                'answered' => $answeredQuestions,
                'unanswered' => $totalQuestions - $answeredQuestions,
                'response_rate' => $questionResponseRate,
            ],
            'metrics' => [
                'question_response_rate' => $questionResponseRate,
            ],
        ];
    }

    /**
     * Calculate admin statistics (global platform metrics)
     */
    protected function calculateAdminStats(): array
    {
        // Global platform statistics with optimized queries
        $platformStats = DB::selectOne("
            SELECT 
                COUNT(DISTINCT CASE WHEN u.role = 'issuer' THEN u.id END) as total_issuers,
                COUNT(DISTINCT CASE WHEN u.role = 'investor' THEN u.id END) as total_investors,
                COUNT(DISTINCT CASE WHEN u.role = 'issuer' AND u.last_login_at > DATE_SUB(NOW(), INTERVAL 30 DAY) THEN u.id END) as active_issuers,
                COUNT(DISTINCT CASE WHEN u.role = 'investor' AND u.last_login_at > DATE_SUB(NOW(), INTERVAL 30 DAY) THEN u.id END) as active_investors
            FROM users u
            WHERE u.role IN ('issuer', 'investor')
        ");

        $meetingStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_meetings,
                COUNT(CASE WHEN m.status = 'scheduled' THEN 1 END) as scheduled_meetings,
                COUNT(CASE WHEN m.status = 'completed' THEN 1 END) as completed_meetings,
                COUNT(CASE WHEN m.status = 'cancelled' THEN 1 END) as cancelled_meetings,
                COUNT(CASE WHEN m.meeting_datetime > NOW() THEN 1 END) as upcoming_meetings,
                COUNT(CASE WHEN DATE(m.created_at) = CURDATE() THEN 1 END) as meetings_today
            FROM meetings m
        ");

        $questionStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_questions,
                COUNT(CASE WHEN q.answer IS NOT NULL AND q.answer != '' THEN 1 END) as answered_questions
            FROM questions q
        ");

        $timeSlotStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_slots,
                COUNT(CASE WHEN ts.is_booked = 0 THEN 1 END) as available_slots,
                COUNT(CASE WHEN ts.is_booked = 1 THEN 1 END) as booked_slots
            FROM time_slots ts
        ");

        // Recent activity for display
        $recentMeetings = DB::select("
            SELECT 
                m.id,
                m.meeting_datetime,
                m.status,
                issuer.first_name as issuer_first_name,
                issuer.last_name as issuer_last_name,
                issuer.company_name,
                investor.first_name as investor_first_name,
                investor.last_name as investor_last_name,
                investor.organization_name
            FROM meetings m
            JOIN users issuer ON m.issuer_id = issuer.id
            JOIN users investor ON m.investor_id = investor.id
            ORDER BY m.created_at DESC
            LIMIT 10
        ");

        $totalQuestions = $questionStats->total_questions ?? 0;
        $answeredQuestions = $questionStats->answered_questions ?? 0;
        $questionResponseRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        $totalMeetings = $meetingStats->total_meetings ?? 0;
        $completedMeetings = $meetingStats->completed_meetings ?? 0;
        $platformEngagementRate = $totalMeetings > 0 ? round(($completedMeetings / $totalMeetings) * 100, 1) : 0;

        return [
            'users' => [
                'total_issuers' => $platformStats->total_issuers ?? 0,
                'total_investors' => $platformStats->total_investors ?? 0,
                'active_issuers' => $platformStats->active_issuers ?? 0,
                'active_investors' => $platformStats->active_investors ?? 0,
            ],
            'meetings' => [
                'total' => $totalMeetings,
                'scheduled' => $meetingStats->scheduled_meetings ?? 0,
                'completed' => $completedMeetings,
                'cancelled' => $meetingStats->cancelled_meetings ?? 0,
                'upcoming' => $meetingStats->upcoming_meetings ?? 0,
                'today' => $meetingStats->meetings_today ?? 0,
                'recent' => $recentMeetings,
            ],
            'questions' => [
                'total' => $totalQuestions,
                'answered' => $answeredQuestions,
                'unanswered' => $totalQuestions - $answeredQuestions,
                'response_rate' => $questionResponseRate,
            ],
            'time_slots' => [
                'total' => $timeSlotStats->total_slots ?? 0,
                'available' => $timeSlotStats->available_slots ?? 0,
                'booked' => $timeSlotStats->booked_slots ?? 0,
            ],
            'metrics' => [
                'platform_engagement_rate' => $platformEngagementRate,
                'question_response_rate' => $questionResponseRate,
            ],
        ];
    }

    /**
     * Invalidate cache for specific user
     */
    public function invalidateUserCache(int $userId, string $role = null): void
    {
        if ($role) {
            Cache::forget(self::CACHE_PREFIX . $role . '_' . $userId);
        } else {
            // Invalidate all possible role caches for this user
            Cache::forget(self::CACHE_PREFIX . 'issuer_' . $userId);
            Cache::forget(self::CACHE_PREFIX . 'investor_' . $userId);
        }
    }

    /**
     * Invalidate all dashboard caches
     */
    public function invalidateAllDashboardCaches(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'admin_global');
        Cache::forget('active_investors_count');

        // Note: Individual user caches will expire naturally or be invalidated on user actions
    }

    /**
     * Warm up cache for specific user
     */
    public function warmUpUserCache(int $userId, string $role): void
    {
        match($role) {
            'issuer' => $this->getIssuerDashboardStats($userId),
            'investor' => $this->getInvestorDashboardStats($userId),
            'admin' => $this->getAdminDashboardStats(),
            default => null,
        };
    }
}
