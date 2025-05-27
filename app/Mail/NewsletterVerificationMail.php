<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\NewsletterSubscription;

class NewsletterVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(NewsletterSubscription $subscription, string $verificationUrl)
    {
        $this->subscription = $subscription;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'hello@dartshop.pl'),
            subject: '🎯 Potwierdź subskrypcję newslettera DartShop',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-verification',
            with: [
                'subscription' => $this->subscription,
                'verificationUrl' => $this->verificationUrl,
                'appName' => config('app.name', 'DartShop')
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
