<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation à un Rendez-vous</title>
    <style>
        /* Base styles */
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f1f5f9;
            margin: 0;
        }

        /* Container */
        .container {
            margin: 0 auto;
            background-color: #ffffff;
            overflow: hidden;
        }

        .header {
            background-color: #2563eb;
            background-image: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 32px 24px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            border-radius: 1rem;
            overflow: hidden;
            background-color: #ffffff;
            width: fit-content;
        }

        .content {
            padding: 36px;
        }

        h1 {
            color: #2563eb;
            margin-top: 0;
            font-size: 26px;
            font-weight: 600;
        }

        h2 {
            color: #0f766e;
            margin-top: 28px;
            margin-bottom: 16px;
            font-size: 20px;
            font-weight: 600;
        }

        p {
            margin: 16px 0;
            color: #475569;
            font-size: 16px;
        }

        /* Meeting details */
        .meeting-details {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 1rem;
            padding: 20px;
            margin: 20px 0;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            margin-bottom: 12px;
            padding-left: 24px;
            position: relative;
        }

        li:before {
            content: "•";
            position: absolute;
            left: 5px;
            color: #2563eb;
            font-size: 18px;
        }

        /* QR Code */
        .qr-code {
            text-align: center;
            margin: 28px 0;
            background-color: #fafaff;
            padding: 20px;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .qr-code img {
            max-width: 200px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            background-color: white;
        }

        .qr-code-label {
            font-size: 14px;
            color: #64748b;
            margin: 10px 0;
        }

        .qr-download {
            margin-top: 18px;
        }

        .download-btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 1rem;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: #2563eb;
        }

        /* Status section */
        .status {
            padding: 16px;
            border-radius: 8px;
            margin: 24px 0;
        }

        .status-pending {
            background-color: #fff7ed;
            border-left: 4px solid #fb923c;
        }

        .status-confirmed {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
        }

        .status-refused {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
            margin-top: 32px;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <!-- You can add logo here -->
                <img src="{{ asset('img/bmce-logo.jpeg') }}" alt="Logo" width="250">
            </div>
            <h1 style="color: white; margin: 0; font-size: 28px;">Invitation à un Rendez-vous</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $meetingInvestor->investor->first_name }} {{ $meetingInvestor->investor->name }},</p>

            <p>Vous êtes invité(e) à participer à un rendez-vous avec
                <strong>{{ $meetingInvestor->meeting->issuer->first_name }}
                    {{ $meetingInvestor->meeting->issuer->name }}</strong> de
                <strong>{{ $meetingInvestor->meeting->issuer->organization->name ?? 'BMCE Invest' }}</strong>.
            </p>

            <h2>Détails du rendez-vous</h2>
            <div class="meeting-details">
                <ul>
                    <li><strong>Date</strong> : {{ $meetingInvestor->meeting->timeSlot->date->format('d/m/Y') }}</li>
                    <li><strong>Heure</strong> : {{ $meetingInvestor->meeting->timeSlot->start_time->format('H:i') }} -
                        {{ $meetingInvestor->meeting->timeSlot->end_time->format('H:i') }}</li>
                    @if ($meetingInvestor->meeting->room)
                        <li><strong>Salle</strong> : {{ $meetingInvestor->meeting->room->name }}</li>
                    @endif
                    <li><strong>Type</strong> :
                        {{ $meetingInvestor->meeting->is_one_on_one ? 'Individuel' : 'Groupe' }}</li>
                </ul>
            </div>

            <h2>Votre QR Code d'accès</h2>
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $meetingInvestor->investor->qr_code }}"
                    alt="QR Code">
                <p class="qr-code-label">Code: <strong>{{ $meetingInvestor->investor->qr_code }}</strong></p>
                <p>Veuillez présenter ce QR Code à l'entrée de la réunion pour confirmer votre identité et accélérer
                    votre enregistrement.</p>
                <p style="font-size: 14px; color: #059669; font-weight: 500; margin-top: 15px;">
                    📎 <strong>Votre QR Code est également disponible en PDF en pièce jointe de cet email.</strong>
                </p>
                <div class="qr-download">
                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $meetingInvestor->investor->qr_code }}&download=1"
                        class="download-btn">Télécharger le QR Code</a>
                </div>
            </div>

            <h2>Statut de votre participation</h2>
            @if ($meetingInvestor->status->value === \App\Enums\InvestorStatus::PENDING->value)
                <div class="status status-pending">
                    <p style="margin: 0;"><strong>En attente</strong> - Vous n'avez pas encore confirmé votre
                        participation. Merci de nous informer de votre présence dès que possible.</p>
                </div>
            @elseif($meetingInvestor->status->value === \App\Enums\InvestorStatus::CONFIRMED->value)
                <div class="status status-confirmed">
                    <p style="margin: 0;"><strong>Confirmé</strong> - Vous avez confirmé votre participation. Nous nous
                        réjouissons de vous y accueillir.</p>
                </div>
            @elseif($meetingInvestor->status->value === \App\Enums\InvestorStatus::REFUSED->value)
                <div class="status status-refused">
                    <p style="margin: 0;"><strong>Refusé</strong> - Vous avez refusé cette invitation. Si c'est une
                        erreur, merci de nous contacter.</p>
                </div>
            @endif

            <div class="footer">
                <p>Si vous avez des questions ou besoin d'informations supplémentaires, n'hésitez pas à nous contacter.
                </p>
                <p>Cordialement,<br>
                    L'équipe {{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>

</html>
