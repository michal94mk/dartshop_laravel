@component('mail::message')
# 👋 Witaj w {{ $appName }}!

Dziękujemy za zapisanie się do naszego newslettera! Jesteś już o krok od otrzymywania najlepszych ofert i nowości ze świata darta.

**Aby dokończyć proces rejestracji, kliknij przycisk poniżej:**

@component('mail::button', ['url' => $verificationUrl])
✅ Potwierdź subskrypcję
@endcomponent

## 🎯 Co zyskujesz zapisując się do newslettera?

- **Ekskluzywne rabaty** - tylko dla subskrybentów
- **Wczesny dostęp** do nowych produktów  
- **Porady ekspertów** - jak poprawić swoją grę
- **Informacje o turniejach** i wydarzeniach
- **Darmową dostawę** przy zamówieniach newsletter

---

> **Ważne:** Link aktywacyjny jest ważny przez 24 godziny. Jeśli nie potwierdzisz subskrypcji w tym czasie, będziesz musiał zapisać się ponownie.

Jeśli przycisk nie działa, skopiuj i wklej poniższy link do przeglądarki:
{{ $verificationUrl }}

Jeśli nie zapisywałeś się do naszego newslettera, zignoruj tę wiadomość.

---

**Nie chcesz już otrzymywać naszych wiadomości?**  
[Wypisz się z newslettera]({{ $unsubscribeUrl }})

Dziękujemy,<br>
{{ $appName }} Team
@endcomponent