<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('QR Code') }} - {{ $user->name }} {{ $user->first_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1f2937;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 0;
            font-size: 16px;
        }
        .qr-section {
            text-align: center;
            margin: 40px 0;
        }
        .qr-code {
            display: inline-block;
            padding: 20px;
            background-color: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .qr-url {
            font-size: 12px;
            color: #6b7280;
            word-break: break-all;
            margin-top: 10px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
        }
        .user-info {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 30px;
        }
        .user-info h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 8px 20px 8px 0;
            color: #6b7280;
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
        }
        .badge {
            background-color: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .generated-date {
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('Personal QR Code') }}</h1>
            <p>{{ __('Issuer Profile') }}</p>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                <img src="{{ $qrCodeBase64 ?? $qrCodeUrl }}" alt="{{ __('QR Code') }}" style="max-width: 300px; height: auto;">
            </div>
            <div class="qr-url">
                <strong>{{ __('QR Code URL') }}:</strong><br>
                {{ $qrContent }}
            </div>
        </div>

        <div class="user-info">
            <h2>{{ __('Personal Information') }}</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">{{ __('Full Name') }}:</div>
                    <div class="info-value">{{ $user->name }} {{ $user->first_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ __('Email') }}:</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ __('User ID') }}:</div>
                    <div class="info-value">{{ $user->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ __('Role') }}:</div>
                    <div class="info-value"><span class="badge">{{ __('Issuer') }}</span></div>
                </div>
                @if($user->organization)
                <div class="info-row">
                    <div class="info-label">{{ __('Organization') }}:</div>
                    <div class="info-value">{{ $user->organization->name }}</div>
                </div>
                @endif
                @if($user->phone)
                <div class="info-row">
                    <div class="info-label">{{ __('Phone') }}:</div>
                    <div class="info-value">{{ $user->phone }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>{{ __('This QR code provides quick access to your issuer profile.') }}</p>
            <p class="generated-date">{{ __('Generated on') }}: {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>
