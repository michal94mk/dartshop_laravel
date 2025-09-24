@component('mail::message')
# Potwierdź subskrypcję newslettera

Witaj,

Dziękujemy za zapisanie się do naszego newslettera **{{ $appName }}**! Jesteś już o krok od otrzymywania najlepszych ofert i nowości ze świata darta.

@component('mail::button', ['url' => $verificationUrl])
✅ Potwierdź subskrypcję
@endcomponent

**Co zyskujesz zapisując się do newslettera:**
- Ekskluzywne rabaty tylko dla subskrybentów
- Wczesny dostęp do nowych produktów przed wszystkimi innymi  
- Porady ekspertów jak poprawić swoją grę w darta
- Informacje o turniejach i wydarzeniach dartowych
- Specjalne promocje i oferty sezonowe

---

**Ważne informacje:**
- Link aktywacyjny jest ważny przez 24 godziny
- Link jest jednorazowy - po użyciu stanie się nieaktywny
- Jeśli nie potwierdzisz subskrypcji w tym czasie, będziesz musiał zapisać się ponownie

**Nie zapisywałeś się do newslettera?**  
Jeśli nie żądałeś subskrypcji, po prostu zignoruj tę wiadomość.

**Potrzebujesz pomocy?**  
Jeśli masz pytania, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}

**Dbamy o Twoją prywatność:**  
Nigdy nie udostępniamy Twoich danych osobowych osobom trzecim.

Pozdrawienia,<br>
{{ $appName }} Team
@endcomponent