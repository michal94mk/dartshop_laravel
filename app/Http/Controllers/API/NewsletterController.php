<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewsletterVerificationMail;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
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
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy token weryfikacyjny'
            ], 422);
        }

        $subscription = NewsletterSubscription::where('verification_token', $request->token)
            ->where('status', 'pending')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowy lub wygasły token weryfikacyjny'
            ], 404);
        }

        $subscription->markAsVerified();

        return response()->json([
            'success' => true,
            'message' => 'Twój adres email został pomyślnie zweryfikowany! Dziękujemy za zapisanie się do newslettera.'
        ]);
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
     * Send verification email
     */
    private function sendVerificationEmail(NewsletterSubscription $subscription, string $token): void
    {
        try {
            $verificationUrl = url("/api/newsletter/verify?token={$token}");
            
            // Log for debugging
            Log::info("Sending newsletter verification email to: {$subscription->email}");
            Log::info("Verification URL: {$verificationUrl}");

            // Send actual email using Laravel Mail
            Mail::to($subscription->email)->send(new NewsletterVerificationMail($subscription, $verificationUrl));
            
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
