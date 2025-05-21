<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends BaseAdminController
{
    /**
     * Display a listing of the messages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return response()->json($messages);
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        return response()->json($message);
    }

    /**
     * Update the message status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:unread,read,replied',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = ContactMessage::findOrFail($id);
        $message->status = $request->status;
        $message->save();

        return response()->json($message);
    }

    /**
     * Mark a message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        if ($message->status === 'unread') {
            $message->status = 'read';
            $message->save();
        }

        return response()->json($message);
    }

    /**
     * Update the message notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNotes(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = ContactMessage::findOrFail($id);
        $message->notes = $request->notes;
        $message->save();

        return response()->json($message);
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json(null, 204);
    }

    /**
     * Send a response to the contact message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function respond(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $contactMessage = ContactMessage::findOrFail($id);
        
        try {
            // Here you would typically send an email to the contact
            // For now, we'll just store the reply
            $contactMessage->reply = $request->message;
            $contactMessage->save();
            
            return response()->json(['success' => true, 'message' => 'Odpowiedź została wysłana']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Błąd podczas wysyłania odpowiedzi: ' . $e->getMessage()], 500);
        }
    }
} 