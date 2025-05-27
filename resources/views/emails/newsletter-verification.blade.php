<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Potwierdź subskrypcję newslettera - {{ $appName }}</title>
    <style>
        /* Inline styles for better email client compatibility */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .email-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .dart-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .email-body h2 {
            color: #1a202c;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .email-body p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.6;
            color: #4a5568;
        }
        
        .verification-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 200px;
        }
        
        .verification-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        
        .feature-item {
            background-color: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .feature-item h4 {
            margin: 0 0 10px 0;
            color: #1a202c;
            font-size: 16px;
            font-weight: 600;
        }
        
        .feature-item p {
            margin: 0;
            font-size: 14px;
            color: #4a5568;
        }
        
        .email-footer {
            background-color: #1a202c;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .email-footer a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            background-color: #2d3748;
            color: #a0aec0;
            padding: 10px;
            border-radius: 50%;
            text-decoration: none;
            width: 40px;
            height: 40px;
            line-height: 20px;
            text-align: center;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 30px 0;
        }
        
        .alternate-link {
            background-color: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        
        .alternate-link p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #4a5568;
        }
        
        .alternate-link code {
            background-color: #edf2f7;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            word-break: break-all;
            color: #2d3748;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .email-header, .email-body {
                padding: 30px 20px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .verification-button {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="dart-icon">🎯</div>
            <h1>{{ $appName }}</h1>
            <p>Profesjonalny sprzęt do dart</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <h2>Witaj w DartShop!</h2>
            
            <p>Dziękujemy za zapisanie się do naszego newslettera! Aby dokończyć proces rejestracji, musisz potwierdzić swój adres email.</p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verification-button">
                    ✉️ Potwierdź adres email
                </a>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <h4>🎯 Najnowsze produkty</h4>
                    <p>Bądź pierwszy, który dowie się o nowościach w naszym asortymencie</p>
                </div>
                <div class="feature-item">
                    <h4>💸 Ekskluzywne promocje</h4>
                    <p>Otrzymuj specjalne rabaty dostępne tylko dla subskrybentów</p>
                </div>
                <div class="feature-item">
                    <h4>📚 Poradniki ekspertów</h4>
                    <p>Ucz się od najlepszych graczy i doskonał swoje umiejętności</p>
                </div>
                <div class="feature-item">
                    <h4>🏆 Wydarzenia i turnieje</h4>
                    <p>Bądź na bieżąco z lokalnymi turniejami i wydarzeniami</p>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <div class="alternate-link">
                <p><strong>Masz problemy z przyciskiem?</strong></p>
                <p>Skopiuj i wklej poniższy link do przeglądarki:</p>
                <code>{{ $verificationUrl }}</code>
            </div>
            
            <p><small><strong>Ważne:</strong> Link weryfikacyjny jest ważny przez 48 godzin. Jeśli nie potwierdzisz adresu w tym czasie, będziesz musiał zapisać się ponownie.</small></p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="YouTube">📺</a>
                <a href="#" title="Twitter">🐦</a>
            </div>
            
            <p>
                <strong>{{ $appName }}</strong><br>
                Twój sklep z akcesoriami do dart<br>
                <a href="mailto:hello@dartshop.pl">hello@dartshop.pl</a> | +48 123 456 789
            </p>
            
            <div class="divider"></div>
            
            <p>
                Otrzymujesz tego emaila, ponieważ zapisałeś się do newslettera na stronie {{ $appName }}.<br>
                Jeśli to nie Ty, możesz zignorować tego emaila.
            </p>
            
            <p>
                <a href="{{ url('/api/newsletter/unsubscribe?email=' . urlencode($subscription->email)) }}">Wypisz się z newslettera</a> |
                <a href="{{ url('/privacy') }}">Polityka prywatności</a> |
                <a href="{{ url('/contact') }}">Kontakt</a>
            </p>
            
            <p style="font-size: 12px; margin-top: 20px;">
                © {{ date('Y') }} {{ $appName }}. Wszelkie prawa zastrzeżone.
            </p>
        </div>
    </div>
</body>
</html> 