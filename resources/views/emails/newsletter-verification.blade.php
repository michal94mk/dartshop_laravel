@component('mail::message')
# Potwierdź subskrypcję newslettera

Witaj,

Dziękujemy za zapisanie się do naszego newslettera! Aby dokończyć proces rejestracji, potwierdź swoją subskrypcję klikając przycisk poniżej.

@component('mail::button', ['url' => $verificationUrl])
Potwierdź subskrypcję
@endcomponent

**Co zyskujesz zapisując się do newslettera:**
- Ekskluzywne rabaty tylko dla subskrybentów
- Wczesny dostęp do nowych produktów  
- Porady ekspertów i informacje o turniejach
- Specjalne promocje i oferty

---

**Ważne informacje:**
- Link aktywacyjny jest ważny przez 24 godziny
- Jeśli nie potwierdzisz subskrypcji w tym czasie, będziesz musiał zapisać się ponownie

Jeśli nie zapisywałeś się do naszego newslettera, zignoruj tę wiadomość.

W razie pytań, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}

Pozdrawienia,<br>
{{ $appName }} Team
@endcomponent