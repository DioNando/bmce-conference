<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>QR Code - {{ $investor->investor->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.5;
            text-align: center;
        }
        h1 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 15px;
        }
        h2 {
            font-size: 16px;
            color: #1e3a8a;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .logo {
            margin-bottom: 20px;
            text-align: center;
        }
        .info-section {
            margin-bottom: 30px;
            text-align: center;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 0 auto 20px;
            border-radius: 5px;
            max-width: 500px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .qr-container {
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .qr-code {
            margin: 0 auto;
            width: 300px;
            height: 300px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .instructions {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #555;
        }
        .qr-id {
            font-family: monospace;
            font-size: 12px;
            background: #f3f4f6;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <!-- Logo or image if needed -->
        <h1>BMCE Invest - Meeting QR Code</h1>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h2>Investor Information</h2>
            <div class="info-item">
                <strong>{{ $investor->investor->name }} {{ $investor->investor->first_name }}</strong>
            </div>

            @if($investor->investor->organization)
                <div class="info-item">
                    {{ $investor->investor->organization->name }}
                </div>
            @endif

            <div class="info-item">
                {{ $investor->investor->email }}
            </div>
        </div>

        <div class="info-box">
            <h2>Meeting Details</h2>
            <div class="info-item">
                <strong>Date:</strong> {{ $meeting->timeSlot->date->format('l, F j, Y') }}
            </div>

            <div class="info-item">
                <strong>Time:</strong> {{ $meeting->timeSlot->start_time->format('H:i') }} - {{ $meeting->timeSlot->end_time->format('H:i') }}
            </div>

            <div class="info-item">
                <strong>Room:</strong> {{ $meeting->room ? $meeting->room->name : 'No Room Assigned' }}
            </div>
        </div>
    </div>

    <div class="qr-container">
        <img class="qr-code" src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($investor->investor->qr_code) }}&amp;size=300x300" alt="QR Code" />
        <div class="qr-id">{{ $investor->investor->qr_code }}</div>
    </div>

    <div class="instructions">
        <p>Please bring this QR code with you to the meeting for quick check-in.</p>
        <p>This QR code belongs to the user and can be used to check in to any meeting they are registered for.</p>
    </div>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }} • BMCE Invest
    </div>
</body>
</html>
