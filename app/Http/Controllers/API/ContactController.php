<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Frontend\ContactRequest;

/**
 * @OA\Tag(
 *     name="Contact",
 *     description="API Endpoints for contact form"
 * )
 */

class ContactController extends Controller
{
    /**
     * Store a new contact message.
     *
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Send contact message",
     *     description="Send a contact message to the admin",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","subject","message"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="subject", type="string", example="Question about products"),
     *             @OA\Property(property="message", type="string", example="I have a question about...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wiadomość została wysłana. Skontaktujemy się z Tobą jak najszybciej."),
     *             @OA\Property(property="data", ref="#/components/schemas/ContactMessage")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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