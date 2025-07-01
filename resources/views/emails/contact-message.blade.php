@component('mail::message')
# New Contact Message

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})  
**Subject:** {{ $contactMessage->subject }}  
**Date:** {{ $contactMessage->created_at->format('Y-m-d H:i:s') }}

---

**Message:**

{{ $contactMessage->message }}

---

@component('mail::button', ['url' => config('app.url') . '/admin/contact-messages/' . $contactMessage->id])
View in Admin Panel
@endcomponent

Thanks,<br>
{{ $appName }} Contact System
@endcomponent
