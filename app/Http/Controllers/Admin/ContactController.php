<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMessage;

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