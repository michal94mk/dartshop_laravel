<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\NewsletterSubscription;

class NewsletterVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;

        // Set queue name for email notifications (same as password reset)
        $this->onQueue('emails');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Log::info('NewsletterVerificationNotification::toMail called', [
            'email' => $notifiable->email,
            'verification_url' => $this->verificationUrl,
            'notification_class' => static::class,
            'queue' => $this->queue ?? 'default'
        ]);

        return (new MailMessage)
            ->subject('🎯 Potwierdź subskrypcję newslettera DartShop')
            ->greeting('Witaj!')
            ->line('Dziękujemy za zapisanie się do naszego newslettera!')
            ->line('Kliknij przycisk poniżej, aby potwierdzić swoją subskrypcję:')
            ->action('Potwierdź subskrypcję', $this->verificationUrl)
            ->line('Link jest ważny przez 24 godziny.')
            ->line('Jeśli nie zapisywałeś się do newslettera, zignoruj tę wiadomość.')
            ->salutation('Pozdrawienia, DartShop Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'verification_url' => $this->verificationUrl,
        ];
    }
}
