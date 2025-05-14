<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends BaseAdminController
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        // Admin view of contact messages with pagination
        $perPage = $this->getPerPage();
        $query = ContactMessage::query()->orderBy('created_at', 'desc');
        
        // Wyszukiwanie przez metodę z BaseAdminController
        $this->applySearch($query, $request, ['name', 'email', 'subject', 'message']);
        
        $messages = $query->paginate($perPage);
        
        return view('admin.contact.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
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
     * Update the specified message with a reply.
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
        
        return redirect()->back()->with('success', 'Reply has been sent.');
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Message has been deleted.');
    }
} 