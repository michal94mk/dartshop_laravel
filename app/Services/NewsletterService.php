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
        try {
            // Generate verification URL - use API endpoint that redirects
            $verificationUrl = config('app.url') . '/api/newsletter/verify-redirect?token=' . $token;
            
            // Use the same approach as working password reset
            $subscription->notify(new \App\Notifications\NewsletterVerificationNotification($verificationUrl));
            
            Log::info('Newsletter verification notification queued successfully', [
                'subscription_id' => $subscription->id,
                'email' => $subscription->email,
                'verification_url' => $verificationUrl,
                'notification_class' => 'NewsletterVerificationNotification',
                'queue' => 'emails',
                'mail_driver' => config('mail.default')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue newsletter verification email', [
                'subscription_id' => $subscription->id,
                'email' => $subscription->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send a welcome email to the user.
     *
     * @param NewsletterSubscription $subscription
     * @return void
     */
    public function sendWelcomeEmail(NewsletterSubscription $subscription): void
    {
        $subscription->notify(new \App\Notifications\NewsletterWelcomeNotification());
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
        try {
            $subscription->notify(new \App\Notifications\NewsletterWelcomeNotification());
            Log::info('Newsletter welcome notification queued successfully', [
                'subscription_id' => $subscription->id,
                'email' => $subscription->email,
                'notification_class' => 'NewsletterWelcomeNotification',
                'queue' => 'emails',
                'mail_driver' => config('mail.default')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue newsletter welcome email', [
                'subscription_id' => $subscription->id,
                'email' => $subscription->email,
                'error' => $e->getMessage()
            ]);
            // Don't throw here - verification was successful
        }
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