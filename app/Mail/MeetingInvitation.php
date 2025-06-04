<?php

namespace App\Mail;

use App\Models\MeetingInvestor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class MeetingInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public MeetingInvestor $meetingInvestor
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $meetingTitle = $this->meetingInvestor->meeting->issuer->organization->name ?? "BMCE Invest";
        return new Envelope(
            subject: "Invitation : Rendez-vous avec {$meetingTitle}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $user = $this->meetingInvestor->investor;

        // Use the existing QR code data from user's qr_code field
        $qrContent = $user->qr_code;

        // Generate QR code URL using external API service (larger size for PDF)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($qrContent);

        // Download the QR code image and convert to base64 for PDF
        try {
            $qrImageData = file_get_contents($qrCodeUrl);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrImageData);
        } catch (\Exception $e) {
            // Fallback to URL if download fails
            $qrCodeBase64 = $qrCodeUrl;
        }

        // Generate PDF content
        $pdf = Pdf::loadView('investor.qr-code.pdf', compact('user', 'qrCodeUrl', 'qrContent', 'qrCodeBase64'));

        // Generate filename
        $filename = 'qr-code-' . strtolower($user->name) . '-' . strtolower($user->first_name) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf')
        ];
    }
}
