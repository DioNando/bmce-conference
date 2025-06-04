<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingInvestor;
use App\Enums\InvestorStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AttendanceController extends Controller
{
    /**
     * Show the QR code scanner page for a specific meeting.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\View\View
     */
    public function showScanner(Meeting $meeting)
    {
        // Get all meeting investors
        $meetingInvestors = $meeting->meetingInvestors()->with('investor.organization')->get();

        // Filter present investors (those who have checked in)
        $presentInvestors = $meetingInvestors->filter(function ($investor) {
            return !is_null($investor->checked_in_at);
        })->sortByDesc('checked_in_at');

        // Filter absent investors (those who haven't checked in yet)
        $absentInvestors = $meetingInvestors->filter(function ($investor) {
            return is_null($investor->checked_in_at);
        })->sortBy(function ($investor) {
            return $investor->investor->name . ' ' . $investor->investor->first_name;
        });

        return view('admin.meetings.attendance.scanner', compact('meeting', 'presentInvestors', 'absentInvestors'));
    }

    /**
     * Verify a QR code for a meeting attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'meeting_id' => 'required|exists:meetings,id',
        ]);

        // Find user by QR code first
        $user = \App\Models\User::where('qr_code', $request->qr_code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid QR code. User not found.'),
            ], 404);
        }

        // Then find the meeting investor record for this user and meeting
        $meetingInvestor = MeetingInvestor::where('investor_id', $user->id)
            ->where('meeting_id', $request->meeting_id)
            ->first();

        if (!$meetingInvestor) {
            return response()->json([
                'success' => false,
                'message' => __('This user is not registered for this meeting.'),
            ], 404);
        }

        // Check if investor has already checked in
        if ($meetingInvestor->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => __('This investor has already checked in at') . ' ' . $meetingInvestor->checked_in_at->format('d/m/Y H:i:s'),
                'already_checked_in' => true,
                'investor' => [
                    'name' => $meetingInvestor->investor->name . ' ' . $meetingInvestor->investor->first_name,
                    'organization' => $meetingInvestor->investor->organization ? $meetingInvestor->investor->organization->name : null,
                    'email' => $meetingInvestor->investor->email,
                ],
                'checked_in_at' => $meetingInvestor->checked_in_at->format('d/m/Y H:i:s'),
            ], 409);
        }

        // Update investor status
        $meetingInvestor->status = InvestorStatus::ATTENDED;
        $meetingInvestor->checked_in_at = now();
        $meetingInvestor->checked_in_by = Auth::id();
        $meetingInvestor->save();

        $investor = $meetingInvestor->investor;

        return response()->json([
            'success' => true,
            'investor' => [
                'name' => $investor->name . ' ' . $investor->first_name,
                'organization' => $investor->organization ? $investor->organization->name : null,
                'email' => $investor->email,
            ],
            'checked_in_at' => $meetingInvestor->checked_in_at->format('d/m/Y H:i:s'),
            'message' => __('Attendance successfully recorded.'),
        ]);
    }

    /**
     * Generate a QR code for a specific meeting investor.
     *
     * @param  \App\Models\Meeting  $meeting
     * @param  \App\Models\MeetingInvestor  $meetingInvestor
     * @return \Illuminate\View\View
     */
    public function showQrCode(Meeting $meeting, MeetingInvestor $investor)
    {
        // Ensure the investor belongs to this meeting
        if ($investor->meeting_id !== $meeting->id) {
            abort(404);
        }

        return view('admin.meetings.attendance.qrcode', compact('meeting', 'investor'));
    }

    /**
     * Get updated investor tables for a meeting.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvestorTables(Meeting $meeting)
    {
        // Get all meeting investors
        $meetingInvestors = $meeting->meetingInvestors()->with('investor.organization')->get();

        // Filter present investors (those who have checked in)
        $presentInvestors = $meetingInvestors->filter(function ($investor) {
            return !is_null($investor->checked_in_at);
        })->sortByDesc('checked_in_at')->values();

        // Filter absent investors (those who haven't checked in yet)
        $absentInvestors = $meetingInvestors->filter(function ($investor) {
            return is_null($investor->checked_in_at);
        })->sortBy(function ($investor) {
            return $investor->investor->name . ' ' . $investor->investor->first_name;
        })->values();

        // Format investor data for JSON response
        $formattedPresent = $presentInvestors->map(function ($investor) {
            return [
                'name' => $investor->investor->name . ' ' . $investor->investor->first_name,
                'organization' => $investor->investor->organization ? $investor->investor->organization->name : null,
                'checked_in_at' => $investor->checked_in_at->format('H:i:s'),
            ];
        });

        $formattedAbsent = $absentInvestors->map(function ($investor) {
            return [
                'name' => $investor->investor->name . ' ' . $investor->investor->first_name,
                'organization' => $investor->investor->organization ? $investor->investor->organization->name : null,
                'status' => [
                    'label' => __($investor->status->label()),
                    'color' => $investor->status->color()
                ]
            ];
        });

        return response()->json([
            'present_count' => $presentInvestors->count(),
            'absent_count' => $absentInvestors->count(),
            'present_investors' => $formattedPresent,
            'absent_investors' => $formattedAbsent
        ]);
    }

    /**
     * Get attendance statistics for a meeting.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceStatistics(Meeting $meeting)
    {
        // Get all meeting investors
        $meetingInvestors = $meeting->meetingInvestors()->with('investor.organization')->get();

        $totalInvited = $meetingInvestors->count();
        $totalPresent = $meetingInvestors->whereNotNull('checked_in_at')->count();
        $totalAbsent = $totalInvited - $totalPresent;
        $attendanceRate = $totalInvited > 0 ? round(($totalPresent / $totalInvited) * 100, 1) : 0;

        // Get status breakdowns
        $statusBreakdown = [];
        foreach (InvestorStatus::all() as $status) {
            $count = $meetingInvestors->where('status', $status)->count();
            $statusBreakdown[$status->value] = [
                'count' => $count,
                'percentage' => $totalInvited > 0 ? round(($count / $totalInvited) * 100, 1) : 0,
                'label' => __($status->label()),
                'color' => $status->color(),
            ];
        }

        // Get organization breakdown
        $organizationBreakdown = $meetingInvestors
            ->groupBy(function ($investor) {
                return $investor->investor->organization ? $investor->investor->organization->name : __('No Organization');
            })
            ->map(function ($group) use ($totalInvited) {
                $count = $group->count();
                $presentCount = $group->whereNotNull('checked_in_at')->count();
                return [
                    'count' => $count,
                    'percentage' => $totalInvited > 0 ? round(($count / $totalInvited) * 100, 1) : 0,
                    'present_count' => $presentCount,
                    'present_percentage' => $count > 0 ? round(($presentCount / $count) * 100, 1) : 0,
                ];
            });

        // Get check-in time data (for charts)
        $checkInTimes = $meetingInvestors
            ->whereNotNull('checked_in_at')
            ->groupBy(function ($investor) {
                return $investor->checked_in_at->format('H:i');
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortKeys();

        return response()->json([
            'total_invited' => $totalInvited,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'attendance_rate' => $attendanceRate,
            'status_breakdown' => $statusBreakdown,
            'organization_breakdown' => $organizationBreakdown,
            'check_in_times' => $checkInTimes,
        ]);
    }

    /**
     * Export meeting attendance data to CSV.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAttendance(Meeting $meeting)
    {
        $meetingInvestors = $meeting->meetingInvestors()->with(['investor.organization', 'checkedInBy'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-' . $meeting->id . '-' . now()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($meetingInvestors) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                __('Name'),
                __('Organization'),
                __('Email'),
                __('Status'),
                __('Check-in Time'),
                __('Checked in by'),
            ]);

            foreach ($meetingInvestors as $investor) {
                fputcsv($file, [
                    $investor->investor->name . ' ' . $investor->investor->first_name,
                    $investor->investor->organization ? $investor->investor->organization->name : __('No Organization'),
                    $investor->investor->email,
                    __($investor->status->label()),
                    $investor->checked_in_at ? $investor->checked_in_at->format('Y-m-d H:i:s') : '',
                    $investor->checkedInBy ? $investor->checkedInBy->name . ' ' . $investor->checkedInBy->first_name : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export meeting attendance data to PDF.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function exportAttendancePdf(Meeting $meeting)
    {
        $meetingInvestors = $meeting->meetingInvestors()->with(['investor.organization', 'checkedInBy'])->get();

        $presentInvestors = $meetingInvestors->filter(function ($investor) {
            return !is_null($investor->checked_in_at);
        })->sortByDesc('checked_in_at');

        $absentInvestors = $meetingInvestors->filter(function ($investor) {
            return is_null($investor->checked_in_at);
        })->sortBy(function ($investor) {
            return $investor->investor->name . ' ' . $investor->investor->first_name;
        });

        $data = [
            'meeting' => $meeting,
            'presentInvestors' => $presentInvestors,
            'absentInvestors' => $absentInvestors,
            'totalInvited' => $meetingInvestors->count(),
            'attendanceRate' => $meetingInvestors->count() > 0
                ? round(($presentInvestors->count() / $meetingInvestors->count()) * 100, 1)
                : 0,
        ];

        $pdf = PDF::loadView('admin.meetings.attendance.pdf', $data);

        return $pdf->download('attendance-' . $meeting->id . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
