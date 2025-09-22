@php
    // Tłumaczenia sposobów dostawy
    $shippingMethods = [
        'courier' => 'Kurier',
        'inpost' => 'InPost Paczkomaty', 
        'pickup' => 'Odbiór osobisty',
        'express' => 'Kurier ekspresowy'
    ];
    
    // Tłumaczenia sposobów płatności
    $paymentMethods = [
        'cod' => 'Płatność przy odbiorze',
        'card' => 'Płatność kartą',
        'blik' => 'BLIK',
        'p24' => 'Przelewy24',
        'stripe' => 'Płatność kartą online',
        'bank_transfer' => 'Przelew bankowy'
    ];
    
    // Tłumaczenia statusów zamówienia
    $orderStatuses = [
        'pending' => 'Oczekujące',
        'processing' => 'W realizacji', 
        'shipped' => 'Wysłane',
        'delivered' => 'Dostarczone',
        'cancelled' => 'Anulowane',
        'refunded' => 'Zwrócone'
    ];
@endphp

@component('mail::message')
# Dziękujemy za złożenie zamówienia!

Witaj {{ $order->first_name }} {{ $order->last_name }},

Twoje zamówienie **{{ $order->order_number }}** zostało pomyślnie złożone i zostało przyjęte do realizacji.

---

**Szczegóły zamówienia:**
- **Numer zamówienia:** {{ $order->order_number }}
- **Status:** {{ $orderStatuses[$order->status->value ?? $order->status] ?? ucfirst($order->status->value ?? $order->status) }}
- **Data zamówienia:** {{ $order->created_at->format('d.m.Y H:i') }}
- **Email:** {{ $order->email }}

**Adres dostawy:**
{{ $order->first_name }} {{ $order->last_name }}  
{{ $order->address }}  
{{ $order->postal_code }} {{ $order->city }}

**Podsumowanie finansowe:**
- **Wartość produktów:** {{ number_format($order->subtotal, 2, ',', ' ') }} zł
@if($order->shipping_cost > 0)
- **Koszt dostawy:** {{ number_format($order->shipping_cost, 2, ',', ' ') }} zł
@else
- **Dostawa:** 0 zł
@endif
- **Do zapłaty:** **{{ number_format($order->total, 2, ',', ' ') }} zł**

**Sposób dostawy:** {{ $shippingMethods[$order->shipping_method] ?? ucfirst($order->shipping_method) }}  
**Sposób płatności:** {{ $paymentMethods[$order->payment_method] ?? ucfirst($order->payment_method) }}

---

**Zamówione produkty:**
@foreach($order->items as $item)
- **{{ $item->product_name }}** - {{ $item->quantity }} szt. × {{ number_format($item->product_price, 2, ',', ' ') }} zł = {{ number_format($item->total_price, 2, ',', ' ') }} zł
@endforeach

---


Czas realizacji zamówienia wynosi zwykle 1-3 dni robocze.

W razie pytań, skontaktuj się z nami pod adresem {{ config('mail.from.address') }}.

Dziękujemy za zakupy!

{{ $appName }} Team
@endcomponent