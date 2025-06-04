<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Meeting #{{ $meeting->id }} - Investors List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.5;
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
        .logo img {
            max-width: 200px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: white;
        }
        .badge-success {
            background-color: #10b981;
        }
        .badge-warning {
            background-color: #f59e0b;
            color: #78350f;
        }
        .badge-error {
            background-color: #ef4444;
        }
        .badge-info {
            background-color: #3b82f6;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="logo">
        <!-- Logo or image if needed -->
        <h1>BMCE Invest - Meeting Information</h1>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h2>Meeting Details - #{{ $meeting->id }}</h2>

            <div class="info-item">
                <span class="info-label">Date:</span>
                {{ $meeting->timeSlot->date->format('l, F j, Y') }}
            </div>

            <div class="info-item">
                <span class="info-label">Time:</span>
                {{ $meeting->timeSlot->start_time->format('H:i') }} - {{ $meeting->timeSlot->end_time->format('H:i') }}
            </div>

            <div class="info-item">
                <span class="info-label">Room:</span>
                {{ $meeting->room ? $meeting->room->name : 'No Room Assigned' }}
                @if($meeting->room)
                (Capacity: {{ $meeting->room->capacity }})
                @endif
            </div>

            <div class="info-item">
                <span class="info-label">Status:</span>
                {{ $meeting->status->label() }}
            </div>

            <div class="info-item">
                <span class="info-label">Meeting Type:</span>
                {{ $meeting->is_one_on_one ? 'One-on-One (individual)' : 'Group' }}
            </div>
        </div>

        <div class="info-box">
            <h2>Issuer Information</h2>

            <div class="info-item">
                <span class="info-label">Name:</span>
                {{ $meeting->issuer->name . ' ' . $meeting->issuer->first_name }}
            </div>

            <div class="info-item">
                <span class="info-label">Email:</span>
                {{ $meeting->issuer->email }}
            </div>

            <div class="info-item">
                <span class="info-label">Organization:</span>
                {{ $meeting->issuer->organization->name ?? 'No Organization' }}
            </div>
        </div>
    </div>

    <h2>Investors List ({{ $meeting->investors->count() }} participants)</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Organization</th>
                <th>Status</th>
                <th>Invitation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meeting->investors as $index => $investor)
                @php
                    $meetingInvestor = $meeting->meetingInvestors
                        ->where('investor_id', $investor->id)
                        ->first();
                    $status = $meetingInvestor ? $meetingInvestor->status->value : 'unknown';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $investor->name . ' ' . $investor->first_name }}</td>
                    <td>{{ $investor->email }}</td>
                    <td>{{ $investor->organization->name ?? 'No Organization' }}</td>
                    <td>
                        @switch($status)
                            @case(\App\Enums\InvestorStatus::CONFIRMED->value)
                                Confirmed
                            @break
                            @case(\App\Enums\InvestorStatus::PENDING->value)
                                Pending
                            @break
                            @case(\App\Enums\InvestorStatus::REFUSED->value)
                                Refused
                            @break
                            @default
                                Unknown
                        @endswitch
                    </td>
                    <td>
                        @if($meetingInvestor && $meetingInvestor->invitation_sent)
                            Sent ({{ $meetingInvestor->invitation_sent_at->format('Y-m-d H:i') }})
                        @else
                            Not sent
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }} • BMCE Invest
    </div>
</body>
</html>
