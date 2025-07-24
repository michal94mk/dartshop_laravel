<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Frontend\ContactRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class ContactController extends BaseApiController
{
    /**
     * Store a new contact message.
     *
     * @param  \App\Http\Requests\Frontend\ContactRequest  $request
     * @return JsonResponse
     */
    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Send contact message');
            
            $contactMessage = ContactMessage::create($request->validated());

            // Send notification to admin
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            Mail::to($adminEmail)->queue(new ContactMessageNotification($contactMessage));

            return $this->createdResponse($contactMessage, 'Wiadomość została wysłana. Skontaktujemy się z Tobą jak najszybciej.');
        } catch (Exception $e) {
            return $this->handleException($e, 'Sending contact message');
        }
    }
} 