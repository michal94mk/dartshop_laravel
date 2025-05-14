<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display contact form for frontend users.
     */
    public function showContactForm()
    {
        return view('frontend.contact');
    }

    /**
     * Process the contact form submission.
     */
    public function submitContactForm(ContactRequest $request)
    {
        // Save contact message to database
        $message = ContactMessage::create($request->validated());
        
        // Optional: Send email notification
        // Mail::to('admin@example.com')->send(new \App\Mail\ContactFormMail($message));

        return redirect()->back()->with('success', 'Wiadomość została wysłana. Skontaktujemy się z Tobą wkrótce.');
    }
} 