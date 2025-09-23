@component('mail::message')
# Resetowanie hasła

Witaj {{ $notifiable->name ?? 'Użytkowniku' }},

Otrzymujesz ten email, ponieważ otrzymaliśmy żądanie resetowania hasła dla Twojego konta w **{{ config('app.name', 'DartShop') }}**.

@component('mail::button', ['url' => $actionUrl])
🔑 Resetuj hasło
@endcomponent

**Ważne informacje:**
- Ten link do resetowania hasła wygaśnie za {{ $count }} minut
- Link jest jednorazowy - po użyciu stanie się nieaktywny
- Jeśli nie żądałeś resetowania hasła, po prostu zignoruj tę wiadomość

---

**Potrzebujesz pomocy?**  
Jeśli masz pytania, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}

**Dbamy o Twoje bezpieczeństwo:**  
Nigdy nie udostępniamy Twoich danych osobowych osobom trzecim.

Pozdrawienia,<br>
{{ config('app.name') }} Team
@endcomponent
