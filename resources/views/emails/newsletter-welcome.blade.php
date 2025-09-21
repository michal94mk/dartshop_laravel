@component('mail::message')
# 🎉 Witaj w społeczności {{ $appName }}!

Dziękujemy za potwierdzenie subskrypcji! Od teraz będziesz otrzymywać najlepsze oferty i nowości ze świata darta bezpośrednio na swoją skrzynkę.

## 🎯 Co Cię czeka?

- **Ekskluzywne promocje** tylko dla subskrybentów
- **Najnowsze produkty** przed wszystkimi innymi
- **Porady ekspertów** jak poprawić swoją grę
- **Informacje o turniejach** i wydarzeniach dartowych

@component('mail::button', ['url' => config('app.url')])
🛒 Sprawdź nasze produkty
@endcomponent

## 🏆 Specjalna oferta powitalna!

Jako nowy subskrybent otrzymujesz **10% rabatu** na pierwsze zamówienie! 
Użyj kodu: **WELCOME10**

---

**Potrzebujesz pomocy?**  
Jeśli masz pytania, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}

**Nie chcesz już otrzymywać naszych wiadomości?**  
[Wypisz się z newslettera]({{ $unsubscribeUrl }})

Miłej zabawy z dartem!<br>
{{ $appName }} Team
@endcomponent