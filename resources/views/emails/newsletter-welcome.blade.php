@component('mail::message')
# Witaj w społeczności {{ $appName }}!

Dziękujemy za potwierdzenie subskrypcji newslettera! Od teraz będziesz otrzymywać najlepsze oferty i nowości ze świata darta bezpośrednio na swoją skrzynkę.

@component('mail::button', ['url' => config('app.url')])
🛒 Sprawdź nasze produkty
@endcomponent

**Co Cię czeka jako subskrybent:**
- Ekskluzywne promocje tylko dla subskrybentów
- Najnowsze produkty przed wszystkimi innymi
- Porady ekspertów jak poprawić swoją grę w darta
- Informacje o turniejach i wydarzeniach dartowych
- Darmowa dostawa przy wybranych zamówieniach

---

**Potrzebujesz pomocy?**  
Jeśli masz pytania, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}

**Nie chcesz już otrzymywać naszych wiadomości?**  
W każdej chwili możesz się wypisać klikając [tutaj]({{ $unsubscribeUrl }})

**Dbamy o Twoją prywatność:**  
Nigdy nie udostępniamy Twoich danych osobowych osobom trzecim.

Miłej zabawy z dartem!<br>
{{ $appName }} Team
@endcomponent