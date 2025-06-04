<?php

namespace App\Http\Controllers\Issuer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeController extends Controller
{
    /**
     * Display the QR code page for the issuer
     */
    public function show()
    {
        $user = Auth::user();

        // Use the existing QR code data from user's qr_code field
        $qrContent = $user->qr_code;

        // Generate QR code URL using external API service
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrContent);

        return view('issuer.qr-code.show', compact('user', 'qrCodeUrl', 'qrContent'));
    }

    /**
     * Download the QR code as PDF
     */
    public function download()
    {
        $user = Auth::user();

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

        // Generate PDF
        $pdf = Pdf::loadView('issuer.qr-code.pdf', compact('user', 'qrCodeUrl', 'qrContent', 'qrCodeBase64'));

        // Generate filename
        $filename = 'qr-code-' . strtolower($user->name) . '-' . strtolower($user->first_name) . '.pdf';

        return $pdf->download($filename);
    }
}
