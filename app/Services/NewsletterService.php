<?php

namespace App\Services;

use App\Models\NewsletterSubscription;
use App\Mail\NewsletterVerificationMail;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class NewsletterService
{
    /**
     * Subscribe to the newsletter and send a verification or welcome email.
     *
     * @param string $email
     * @return NewsletterSubscription
     * @throws Exception
     */
    public function subscribe(string $email): NewsletterSubscription
    {
        try {
            $subscription = NewsletterSubscription::firstOrNew(['email' => $email]);
            if ($subscription->isActive()) {
                throw new Exception('Ten adres email jest już zapisany do newslettera');
            }
            if ($subscription->isPending()) {
                $token = $subscription->generateVerificationToken();
                $this->sendVerificationEmail($subscription, $token);
                return $subscription;
            }
            $subscription->status = 'pending';
            $subscription->save();
            $token = $subscription->generateVerificationToken();
            $this->sendVerificationEmail($subscription, $token);
            return $subscription;
        } catch (Exception $e) {
            Log::error('NewsletterService error', [
                'message' => $e->getMessage(),
                'email' => $email,
            ]);
            throw $e;
        }
    }

    /**
     * Send a verification email to the user.
     *
     * @param NewsletterSubscription $subscription
     * @param string $token
     * @return void
     */
    public function sendVerificationEmail(NewsletterSubscription $subscription, string $token): void
    {
        Mail::to($subscription->email)->queue(new NewsletterVerificationMail($subscription, $token));
    }

    /**
     * Send a welcome email to the user.
     *
     * @param NewsletterSubscription $subscription
     * @return void
     */
    public function sendWelcomeEmail(NewsletterSubscription $subscription): void
    {
        Mail::to($subscription->email)->queue(new NewsletterWelcomeMail($subscription));
    }

    /**
     * Verify newsletter subscription by token.
     *
     * @param string $token
     * @return bool True if verification succeeded, false otherwise.
     */
    public function verifySubscription(string $token): bool
    {
        $subscription = \App\Models\NewsletterSubscription::where('verification_token', $token)
            ->where('status', 'pending')
            ->first();
        if (!$subscription) {
            return false;
        }
        $subscription->markAsVerified();
        // Send welcome email
        \Mail::to($subscription->email)->queue(new \App\Mail\NewsletterWelcomeMail($subscription));
        return true;
    }

    /**
     * Unsubscribe from the newsletter by email.
     *
     * @param string $email
     * @return bool True if unsubscribe succeeded, false otherwise.
     */
    public function unsubscribe(string $email): bool
    {
        $subscription = \App\Models\NewsletterSubscription::where('email', $email)
            ->where('status', 'active')
            ->first();
        if (!$subscription) {
            return false;
        }
        $subscription->unsubscribe();
        return true;
    }
} 