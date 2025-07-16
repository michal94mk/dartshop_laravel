<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Witaj w {{ $appName }}!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #3b82f6;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎯 {{ $appName }}</div>
        </div>
        
        <div class="content">
            <h1>Witaj w społeczności {{ $appName }}! 🎉</h1>
            
            <p>Cześć!</p>
            
            <p>Dziękujemy za zapisanie się do naszego newslettera! Jesteśmy bardzo podekscytowani, że dołączasz do naszej społeczności.</p>
            
            <p><strong>Co Cię czeka:</strong></p>
            <ul>
                <li>🔥 Ekskluzywne oferty i promocje</li>
                <li>📦 Informacje o nowych produktach</li>
                <li>💡 Porady i inspiracje</li>
                <li>🎁 Specjalne rabaty tylko dla subskrybentów</li>
            </ul>
            
            <p>Zacznij już teraz przeglądanie naszego sklepu:</p>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="button">Przejdź do sklepu</a>
            </div>
            
            <p>Jeśli masz jakiekolwiek pytania, śmiało skontaktuj się z nami. Jesteśmy tutaj, aby Ci pomóc!</p>
            
            <p>Z pozdrowieniami,<br>
            Zespół {{ $appName }}</p>
        </div>
        
        <div class="footer">
            <p>Otrzymujesz ten email, ponieważ zapisałeś się do newslettera {{ $appName }}.</p>
            <p>
                <a href="{{ config('app.url') }}/newsletter/unsubscribe">Wypisz się z newslettera</a>
            </p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html> 