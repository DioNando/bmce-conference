<?php

namespace App\Exports;

use App\Models\Meeting;
use App\Models\MeetingInvestor;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeExport
{
    protected $meeting;
    protected $meetingInvestor;

    public function __construct(Meeting $meeting, MeetingInvestor $meetingInvestor)
    {
        $this->meeting = $meeting;
        $this->meetingInvestor = $meetingInvestor;
    }

    /**
     * Generate PDF for QR code
     *
     * @return \Barryvdh\DomPDF\PDF
     */
    public function toPdf()
    {
        // Load meeting investor with all necessary relationships
        $meetingInvestor = $this->meetingInvestor->load([
            'investor',
            'investor.organization',
            'meeting.timeSlot',
            'meeting.room'
        ]);

        $meeting = $this->meeting;

        // Generate PDF
        $pdf = PDF::loadView('exports.qrcode', [
            'meeting' => $meeting,
            'investor' => $meetingInvestor
        ]);

        // Set paper size and orientation - A4 size would be good for a QR code
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
        $investorName = str_replace(' ', '_', $this->meetingInvestor->investor->name);
        $filename = 'qrcode_meeting_' . $this->meeting->id . '_' . $investorName . '_' . now()->format('Y-m-d') . '.pdf';

        return $this->toPdf()->download($filename);
    }

    /**
     * Stream PDF in browser
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stream()
    {
        $investorName = str_replace(' ', '_', $this->meetingInvestor->investor->name);
        $filename = 'qrcode_meeting_' . $this->meeting->id . '_' . $investorName . '_' . now()->format('Y-m-d') . '.pdf';

        return $this->toPdf()->stream($filename);
    }
}
