<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    public function submitContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Save contact message to database
        ContactMessage::create($validated);
        
        // Optional: Send email notification
        // Mail::to('admin@example.com')->send(new \App\Mail\ContactFormMail($validated));

        return redirect()->back()->with('success', 'Wiadomość została wysłana. Skontaktujemy się z Tobą wkrótce.');
    }
} 