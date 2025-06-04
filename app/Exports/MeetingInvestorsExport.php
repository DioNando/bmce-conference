<?php

namespace App\Exports;

use App\Models\Meeting;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class MeetingInvestorsExport
{
    protected $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Generate PDF for meeting with investors list
     *
     * @return \Barryvdh\DomPDF\PDF
     */
    public function toPdf()
    {
        // Load meeting with all necessary relationships
        $meeting = $this->meeting->load([
            'issuer',
            'issuer.organization',
            'timeSlot',
            'room',
            'investors',
            'investors.organization',
            'meetingInvestors'
        ]);

        // Generate PDF
        $pdf = PDF::loadView('exports.meeting-investors', [
            'meeting' => $meeting
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Download PDF with custom filename
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download()
    {
        $filename = 'meeting_' . $this->meeting->id . '_investors_' . now()->format('Y-m-d_His') . '.pdf';

        return $this->toPdf()->download($filename);
    }

    /**
     * Stream PDF in browser
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stream()
    {
        $filename = 'meeting_' . $this->meeting->id . '_investors_' . now()->format('Y-m-d_His') . '.pdf';

        return $this->toPdf()->stream($filename);
    }
}
