<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activation de votre compte</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        /* Header */
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

        p {
            margin: 16px 0;
            color: #475569;
            font-size: 16px;
        }

        /* Credentials Box */
        .credentials {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin: 28px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .credentials h2 {
            color: #0284c7;
            margin-top: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .credentials p {
            margin: 10px 0;
            font-size: 15px;
        }

        /* Button */
        .button {
            display: inline-block;
            background-color: #2563eb;
            background-image: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 1rem;
            margin: 24px 0;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
        }

        .button:hover {
            background-image: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 6px 8px rgba(37, 99, 235, 0.3);
            transform: translateY(-1px);
        }

        /* Important Notice */
        .important {
            background-color: #fef9c3;
            border-left: 4px solid #eab308;
            padding: 12px 16px;
            border-radius: 4px;
            margin: 24px 0;
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
        <div class="header" style="padding: 32px 24px; justify-content: space-between; align-items: center;">
            <div class="logo"
                style="border-radius: 1rem; overflow: hidden; background-color: #ffffff; width: fit-content;">
                <!-- You can add logo here -->
                <img src="{{ asset('img/bmce-logo.jpeg') }}" alt="Logo" width="250">
            </div>
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ config('app.name') }}</h1>
        </div>

        <div class="content" style="padding: 36px; border-radius: 1rem;">
            <h1>Activation de votre compte</h1>

            <p>Bonjour {{ $user->first_name }} {{ $user->name }},</p>

            <p>Votre compte a été créé avec succès sur la plateforme BMCE Invest. Nous sommes ravis de vous compter
                parmi nos utilisateurs!</p>

            <div class="credentials">
                <h2>Vos identifiants de connexion</h2>
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Mot de passe temporaire :</strong> {{ $password }}</p>
            </div>

            <p>Pour accéder à votre espace personnel, veuillez cliquer sur le bouton ci-dessous :</p>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button" style="border-radius: 1rem;">Se connecter à mon compte</a>
            </div>

            <div class="important">
                <p style="margin: 0;"><strong>Important :</strong> Pour des raisons de sécurité, nous vous recommandons
                    de changer immédiatement votre mot de passe temporaire lors de votre première connexion.</p>
            </div>

            <div class="footer">
                <p>Si vous rencontrez des difficultés pour vous connecter, n'hésitez pas à contacter notre équipe de
                    support.</p>
                <p>Cordialement,<br>
                    L'équipe {{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>

</html>
