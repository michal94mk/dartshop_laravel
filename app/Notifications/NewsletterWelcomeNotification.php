<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\NewsletterSubscription;

class NewsletterWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
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
        \Log::info('NewsletterWelcomeNotification::toMail called', [
            'email' => $notifiable->email,
            'notification_class' => static::class,
            'queue' => $this->queue ?? 'default'
        ]);

        $unsubscribeUrl = config('app.url') . '/newsletter/unsubscribe?email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('🎯 Witaj w społeczności DartShop!')
            ->greeting('Witaj!')
            ->line('Dziękujemy za potwierdzenie subskrypcji newslettera!')
            ->line('Od teraz będziesz otrzymywać nasze najlepsze oferty i nowości.')
            ->action('Sprawdź nasze produkty', config('app.url'))
            ->line('**Specjalna oferta powitalna:**')
            ->line('Jako nowy subskrybent otrzymujesz **10% rabatu** na pierwsze zamówienie!')
            ->line('Użyj kodu: **WELCOME10** podczas składania zamówienia.')
            ->line('---')
            ->line('**Potrzebujesz pomocy?** Skontaktuj się z nami pod adresem ' . config('mail.from.address'))
            ->line('**Nie chcesz już otrzymywać naszych wiadomości?** [Wypisz się](' . $unsubscribeUrl . ')')
            ->salutation('Miłej zabawy z dartem! DartShop Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
