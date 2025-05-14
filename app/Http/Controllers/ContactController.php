<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMessage;
use App\Http\Controllers\Admin\BaseAdminController;

class ContactController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admin view of contact messages with pagination
        $perPage = $this->getPerPage();
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('admin.contact.index', compact('messages'));
    }

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

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $message)
    {
        // Mark message as read if unread
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
        
        return response()->json($message);
    }

    /**
     * Update the specified resource with a reply.
     */
    public function reply(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'reply' => 'required|string',
        ]);
        
        $message->update([
            'reply' => $validated['reply'],
            'status' => 'replied'
        ]);
        
        // Optional: Send reply email to the user
        // Mail::to($message->email)->send(new \App\Mail\ContactReplyMail($message));
        
        return redirect()->back()->with('success', 'Odpowiedź została wysłana.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Wiadomość została usunięta.');
    }
}
