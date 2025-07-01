@component('mail::message')
# Order Status Update

Hello {{ $order->first_name }} {{ $order->last_name }},

Your order **{{ $order->order_number }}** status has been updated.

**Status changed from:** {{ ucfirst($oldStatus) }}  
**Status changed to:** {{ ucfirst($newStatus) }}  
**Date:** {{ now()->format('Y-m-d H:i:s') }}

@if($note)
**Additional Notes:**
{{ $note }}
@endif

---

**Order Details:**
- **Total:** ${{ number_format($order->total_amount, 2) }}
- **Items:** {{ $order->items->count() }} item(s)
- **Ordered:** {{ $order->created_at->format('Y-m-d H:i:s') }}

@component('mail::button', ['url' => config('app.url') . '/profile/orders/' . $order->id])
View Order Details
@endcomponent

Thanks,<br>
{{ $appName }} Team
@endcomponent
