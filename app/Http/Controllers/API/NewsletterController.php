<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewsletterVerificationMail;
use App\Mail\NewsletterWelcomeMail;

/**
 * @OA\Tag(
 *     name="Newsletter",
 *     description="API Endpoints for newsletter management"
 * )
 */

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     *
     * @OA\Post(
     *     path="/api/newsletter/subscribe",
     *     summary="Subscribe to newsletter",
     *     description="Subscribe an email address to the newsletter",
     *     tags={"Newsletter"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sprawdź swoją skrzynkę email i kliknij link weryfikacyjny")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Email already subscribed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ten adres email jest już zapisany do newslettera")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function subscribe(Request $request): JsonResponse
    {
        // Debug logging
        Log::info('Newsletter subscribe endpoint hit', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowe dane',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Check if email already exists
        $existingSubscription = NewsletterSubscription::where('email', $email)->first();

        if ($existingSubscription) {
            if ($existingSubscription->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ten adres email jest już zapisany do newslettera'
                ], 409);
            }

            if ($existingSubscription->isPending()) {
                // Resend verification email
                $token = $existingSubscription->generateVerificationToken();
                $this->sendVerificationEmail($existingSubscription, $token);

                return response()->json([
                    'success' => true,
                    'message' => 'Link weryfikacyjny został ponownie wysłany na Twój adres email'
                ]);
            }

            // If unsubscribed, reactivate
            if ($existingSubscription->status === 'unsubscribed') {
                $existingSubscription->status = 'pending';
                $existingSubscription->unsubscribed_at = null;
                $existingSubscription->save();

                $token = $existingSubscription->generateVerificationToken();
                $this->sendVerificationEmail($existingSubscription, $token);

                return response()->json([
                    'success' => true,
                    'message' => 'Sprawdź swoją skrzynkę email i kliknij link weryfikacyjny'
                ]);
            }
        }

        // Create new subscription
        $subscription = NewsletterSubscription::create([
            'email' => $email,
            'status' => 'pending'
        ]);

        $token = $subscription->generateVerificationToken();
        $this->sendVerificationEmail($subscription, $token);

        $response = [
            'success' => true,
            'message' => 'Sprawdź swoją skrzynkę email i kliknij link weryfikacyjny'
        ];

        Log::info('Newsletter subscribe success response', [
            'response' => $response,
            'subscription_id' => $subscription->id,
            'email' => $subscription->email
        ]);

        return response()->json($response);
    }

    /**
     * Verify email subscription
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect('/newsletter/verify?status=error');
        }

        $subscription = NewsletterSubscription::where('verification_token', $request->token)
            ->where('status', 'pending')
            ->first();

        if (!$subscription) {
            return redirect('/newsletter/verify?status=error');
        }

        $subscription->markAsVerified();

        // Send welcome email
        Mail::to($subscription->email)->queue(new NewsletterWelcomeMail($subscription));

        // Przekieruj na stronę z podziękowaniem
        return redirect('/newsletter/verified');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy adres email'
            ], 422);
        }

        $subscription = NewsletterSubscription::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Nie znaleziono aktywnej subskrypcji dla tego adresu email'
            ], 404);
        }

        $subscription->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'Zostałeś pomyślnie wypisany z newslettera'
        ]);
    }

    /**
     * Check subscription status
     */
    public function status(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy adres email'
            ], 422);
        }

        $subscription = NewsletterSubscription::where('email', $request->email)->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'subscribed' => false,
                'status' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'subscribed' => $subscription->isActive(),
            'status' => $subscription->status,
            'verified_at' => $subscription->verified_at?->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Send verification email via queued job
     * 
     * NOTE: W środowisku produkcyjnym zalecane jest uwierzytelnienie własnej domeny
     * w Brevo dla lepszej dostarczalności maili (SPF/DKIM/DMARC compliance)
     */
    private function sendVerificationEmail(NewsletterSubscription $subscription, string $token): void
    {
        try {
            $verificationUrl = url("/api/newsletter/verify?token={$token}");
            
            // Log for debugging
            Log::info("Sending newsletter verification email to: {$subscription->email}");
            Log::info("Verification URL: {$verificationUrl}");

            // Send actual email using Laravel Mail with queue
            Mail::to($subscription->email)->queue(new NewsletterVerificationMail($subscription, $verificationUrl));
            
            Log::info("Newsletter verification email sent successfully to: {$subscription->email}");
            
        } catch (\Exception $e) {
            Log::error("Failed to send newsletter verification email to {$subscription->email}: " . $e->getMessage(), [
                'exception' => $e,
                'email' => $subscription->email,
                'token' => $token
            ]);
            
            // Don't throw the exception - we don't want to break the subscription process
            // The user will still be subscribed, they just won't get the email
        }
    }
}
