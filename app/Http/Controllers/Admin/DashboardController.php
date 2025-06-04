<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\Organization;
use App\Models\Room;
use App\Models\User;
use App\Models\Country;
use App\Models\Meeting;
use App\Models\Question;
use App\Models\MeetingInvestor;
use App\Enums\MeetingStatus;
use App\Enums\OrganizationType;
use App\Enums\InvestorStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Récupérer les utilisateurs avec les rôles issuer et investor dans une collection combinée
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value]);
        })->with(['roles', 'organization'])->get();

        // === Statistiques de base ===
        $totalUsers = User::count();
        $totalOrganizations = Organization::count();
        $totalMeetings = Meeting::count();
        $totalTimeSlots = TimeSlot::count();
        $totalRooms = Room::count();
        $totalQuestions = Question::count();

        // === Statistiques par rôle ===
        $issuersCount = User::role(UserRole::ISSUER->value)->count();
        $investorsCount = User::role(UserRole::INVESTOR->value)->count();
        $adminsCount = User::role('admin')->count();

        // === Statistiques des meetings ===
        $meetings = Meeting::with(['timeSlot', 'meetingInvestors'])->get();

        // Meetings par statut
        $meetingsByStatus = [
            'upcoming' => $meetings->filter(fn($m) => $m->timeSlot && $m->timeSlot->start_time > now())->count(),
            'completed' => $meetings->filter(fn($m) => $m->timeSlot && $m->timeSlot->end_time < now())->count(),
            'scheduled' => $meetings->where('status', MeetingStatus::SCHEDULED)->count(),
            'cancelled' => $meetings->where('status', MeetingStatus::CANCELLED)->count(),
            'pending' => $meetings->where('status', MeetingStatus::PENDING)->count(),
        ];

        // === Statistiques des participants ===
        $meetingInvestors = MeetingInvestor::all();
        $participantStats = [
            'confirmed' => $meetingInvestors->where('status', InvestorStatus::CONFIRMED)->count(),
            'pending' => $meetingInvestors->where('status', InvestorStatus::PENDING)->count(),
            'attended' => $meetingInvestors->where('status', InvestorStatus::ATTENDED)->count(),
            'refused' => $meetingInvestors->where('status', InvestorStatus::REFUSED)->count(),
            'absent' => $meetingInvestors->where('status', InvestorStatus::ABSENT)->count(),
        ];

        // Taux de participation
        $totalParticipants = $meetingInvestors->count();
        $participationRates = [
            'confirmation_rate' => $totalParticipants > 0 ? round(($participantStats['confirmed'] / $totalParticipants) * 100, 1) : 0,
            'attendance_rate' => $totalParticipants > 0 ? round(($participantStats['attended'] / $totalParticipants) * 100, 1) : 0,
            'refusal_rate' => $totalParticipants > 0 ? round(($participantStats['refused'] / $totalParticipants) * 100, 1) : 0,
        ];

        // === Statistiques des questions ===
        $questions = Question::all();
        $questionStats = [
            'total' => $questions->count(),
            'answered' => $questions->whereNotNull('answer')->count(),
            'unanswered' => $questions->whereNull('answer')->count(),
        ];
        $questionStats['response_rate'] = $questionStats['total'] > 0 ?
            round(($questionStats['answered'] / $questionStats['total']) * 100, 1) : 0;

        // === Statistiques des time slots ===
        $timeSlots = TimeSlot::with('meetings')->get();
        $timeSlotStats = [
            'total' => $timeSlots->count(),
            'available' => $timeSlots->filter(fn($ts) => $ts->meetings->isEmpty())->count(),
            'booked' => $timeSlots->filter(fn($ts) => $ts->meetings->isNotEmpty())->count(),
        ];
        $timeSlotStats['utilization_rate'] = $timeSlotStats['total'] > 0 ?
            round(($timeSlotStats['booked'] / $timeSlotStats['total']) * 100, 1) : 0;

        // === Statistiques des organisations ===
        $organizations = Organization::with('users')->get();
        $organizationStats = [
            'with_issuers' => $organizations->filter(fn($org) => $org->users->where('roles.0.name', UserRole::ISSUER->value)->isNotEmpty())->count(),
            'with_investors' => $organizations->filter(fn($org) => $org->users->where('roles.0.name', UserRole::INVESTOR->value)->isNotEmpty())->count(),
            'mixed' => $organizations->filter(fn($org) =>
                $org->users->where('roles.0.name', UserRole::ISSUER->value)->isNotEmpty() &&
                $org->users->where('roles.0.name', UserRole::INVESTOR->value)->isNotEmpty()
            )->count(),
        ];

        // === Métriques d'activité (dernières 24h) ===
        $last24Hours = now()->subDay();
        $activityMetrics = [
            'new_users_24h' => User::where('created_at', '>=', $last24Hours)->count(),
            'new_meetings_24h' => Meeting::where('created_at', '>=', $last24Hours)->count(),
            'new_questions_24h' => Question::where('created_at', '>=', $last24Hours)->count(),
            // 'logins_24h' => User::where('last_login_at', '>=', $last24Hours)->count(),
        ];

        // === Top performers ===
        $topIssuers = User::role(UserRole::ISSUER->value)
            ->withCount(['issuerMeetings as meetings_count'])
            ->orderBy('meetings_count', 'desc')
            ->limit(5)
            ->get();

        $topInvestors = User::role(UserRole::INVESTOR->value)
            ->withCount(['investorMeetings as meetings_count'])
            ->orderBy('meetings_count', 'desc')
            ->limit(5)
            ->get();

        // === Évolution mensuelle - Optimized version ===
        $monthlyEvolution = [];

        // Get data for the last 6 months in bulk
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $today = now()->endOfMonth();

        // Get monthly user counts by role
        $monthlyUserCounts = DB::table('users')
            ->join('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [UserRole::ISSUER->value, UserRole::INVESTOR->value])
            ->whereBetween('users.created_at', [$sixMonthsAgo, $today])
            ->select(
                DB::raw('YEAR(users.created_at) as year'),
                DB::raw('MONTH(users.created_at) as month'),
                'roles.name as role',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('YEAR(users.created_at)'), DB::raw('MONTH(users.created_at)'), 'roles.name')
            ->get();

        // Get monthly meeting and organization counts
        $monthlyMeetings = Meeting::whereBetween('created_at', [$sixMonthsAgo, $today])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get();

        $monthlyOrganizations = Organization::whereBetween('created_at', [$sixMonthsAgo, $today])
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get();

        // Convert to associative arrays for quick lookup
        $userCountsByMonth = [];
        foreach ($monthlyUserCounts as $user) {
            $key = $user->year . '-' . str_pad($user->month, 2, '0', STR_PAD_LEFT);
            $userCountsByMonth[$key][$user->role] = $user->count;
        }

        $meetingCountsByMonth = [];
        foreach ($monthlyMeetings as $meeting) {
            $key = $meeting->year . '-' . str_pad($meeting->month, 2, '0', STR_PAD_LEFT);
            $meetingCountsByMonth[$key] = $meeting->count;
        }

        $organizationCountsByMonth = [];
        foreach ($monthlyOrganizations as $org) {
            $key = $org->year . '-' . str_pad($org->month, 2, '0', STR_PAD_LEFT);
            $organizationCountsByMonth[$key] = $org->count;
        }

        // Build the evolution data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');

            $totalUsers = ($userCountsByMonth[$key][UserRole::ISSUER->value] ?? 0) +
                         ($userCountsByMonth[$key][UserRole::INVESTOR->value] ?? 0);

            $monthlyEvolution[] = [
                'month' => $date->format('M Y'),
                'new_users' => $totalUsers,
                'new_meetings' => $meetingCountsByMonth[$key] ?? 0,
                'new_organizations' => $organizationCountsByMonth[$key] ?? 0,
            ];
        }

        // Consolidation des statistiques
        $stats = [
            'totalUsers' => $totalUsers,
            'totalOrganizations' => $totalOrganizations,
            'totalMeetings' => $totalMeetings,
            'issuersCount' => $issuersCount,
            'investorsCount' => $investorsCount,
            'adminsCount' => $adminsCount,
            'timeSlots' => $totalTimeSlots,
            'rooms' => $totalRooms,
            'totalQuestions' => $totalQuestions,
            'meetingsByStatus' => $meetingsByStatus,
            'participantStats' => $participantStats,
            'participationRates' => $participationRates,
            'questionStats' => $questionStats,
            'timeSlotStats' => $timeSlotStats,
            'organizationStats' => $organizationStats,
            'activityMetrics' => $activityMetrics,
            'topIssuers' => $topIssuers,
            'topInvestors' => $topInvestors,
            'monthlyEvolution' => $monthlyEvolution,
        ];

        // Data for Meeting Status Chart
        $meetingStatusStats = Meeting::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->total];
            });

        // Data for Registration Evolution Chart - Optimized version
        $registrationEvolution = [
            'labels' => [],
            'issuers' => [],
            'investors' => [],
        ];

        // Get the date range for the last 30 days
        $startDate = Carbon::today()->subDays(29);
        $endDate = Carbon::today();

        // Optimize: Get all users with roles created in the last 30 days in one query
        $usersWithRoles = DB::table('users')
            ->join('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [UserRole::ISSUER->value, UserRole::INVESTOR->value])
            ->whereBetween('users.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(users.created_at) as date'),
                'roles.name as role',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(users.created_at)'), 'roles.name')
            ->get();

        // Convert to associative array for quick lookup
        $usersByDateAndRole = [];
        foreach ($usersWithRoles as $user) {
            $usersByDateAndRole[$user->date][$user->role] = $user->count;
        }

        // Build the evolution data
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $registrationEvolution['labels'][] = $date->format('M d');
            $registrationEvolution['issuers'][] = $usersByDateAndRole[$dateString][UserRole::ISSUER->value] ?? 0;
            $registrationEvolution['investors'][] = $usersByDateAndRole[$dateString][UserRole::INVESTOR->value] ?? 0;
        }

        // Data for Organization Type Chart
        $organizationTypeStats = Organization::select('organization_type', DB::raw('count(*) as total'))
            ->groupBy('organization_type')
            ->get()
            ->mapWithKeys(function ($item) {
                $typeValue = $item->organization_type instanceof OrganizationType ? $item->organization_type->value : (string) $item->organization_type;
                return [$typeValue => $item->total];
            });

        // Récupérer les données des pays pour la carte
        $usersByCountry = DB::table('users')
            ->join('organizations', 'users.organization_id', '=', 'organizations.id')
            ->join('countries', 'organizations.country_id', '=', 'countries.id')
            ->select('countries.name_en as country', 'countries.code as code', DB::raw('count(*) as total'))
            ->groupBy('countries.name_en', 'countries.code')
            ->orderBy('total', 'desc')
            ->get();

        // Récupérer tous les pays de la base de données
        $allCountries = Country::select('code', 'name_fr', 'name_en')->get()
            ->keyBy('code') // Transformer en collection indexée par code
            ->toArray();

        return view('admin.dashboard', compact('stats', 'users', 'usersByCountry', 'allCountries', 'meetingStatusStats', 'registrationEvolution', 'organizationTypeStats'));
    }
}
