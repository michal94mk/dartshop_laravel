@component('mail::message')
# Dziękujemy za złożenie zamówienia!

Witaj {{ $order->first_name }} {{ $order->last_name }},

Twoje zamówienie **{{ $order->order_number }}** zostało pomyślnie złożone i zostało przyjęte do realizacji.

---

**Szczegóły zamówienia:**
- **Numer zamówienia:** {{ $order->order_number }}
- **Status:** {{ ucfirst($order->status) }}
- **Data zamówienia:** {{ $order->created_at->format('d.m.Y H:i') }}
- **Email:** {{ $order->email }}

**Adres dostawy:**
{{ $order->first_name }} {{ $order->last_name }}  
{{ $order->address }}  
{{ $order->postal_code }} {{ $order->city }}

**Podsumowanie finansowe:**
- **Wartość produktów:** {{ number_format($order->subtotal, 2) }} zł
@if($order->shipping_cost > 0)
- **Koszt dostawy:** {{ number_format($order->shipping_cost, 2) }} zł
@endif
@if($order->discount > 0)
- **Rabat:** -{{ number_format($order->discount, 2) }} zł
@endif
- **Do zapłaty:** **{{ number_format($order->total, 2) }} zł**

**Sposób dostawy:** {{ ucfirst($order->shipping_method) }}  
**Sposób płatności:** {{ ucfirst($order->payment_method) }}

---

**Zamówione produkty:**
@foreach($order->items as $item)
- **{{ $item->product_name }}** - {{ $item->quantity }} szt. × {{ number_format($item->product_price, 2) }} zł = {{ number_format($item->total_price, 2) }} zł
@endforeach

---

@if($order->user_id)
@component('mail::button', ['url' => config('app.url') . '/profile/orders/' . $order->id])
Zobacz szczegóły zamówienia
@endcomponent
@endif

Czas realizacji zamówienia wynosi zwykle 1-3 dni robocze. O każdej zmianie statusu zamówienia poinformujemy Cię emailem.

W razie pytań, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}.

Dziękujemy za zakupy!

{{ $appName }} Team
@endcomponent
