<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Frontend\ContactRequest;

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     *
     * @param  \App\Http\Requests\Frontend\ContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        $contactMessage = ContactMessage::create($request->validated());

        // Send notification to admin
        $adminEmail = config('mail.admin_email', config('mail.from.address'));
        Mail::to($adminEmail)->queue(new ContactMessageNotification($contactMessage));

        return response()->json([
            'success' => true,
            'message' => 'Wiadomość została wysłana. Skontaktujemy się z Tobą jak najszybciej.',
            'data' => $contactMessage
        ]);
    }
} 