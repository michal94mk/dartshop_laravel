<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class QueuedResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a notification instance.
     */
    public function __construct($token)
    {
        parent::__construct($token);
        
        // Set queue name for email notifications
        $this->onQueue('emails');
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        // Force HTTPS for production environments
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Create frontend reset password URL
        $frontendUrl = config('app.url');
        
        return $frontendUrl . '/reset-password/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());
    }

    /**
     * Get the reset password notification mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        \Log::info('QueuedResetPasswordNotification::toMail called with Polish template');
        return $this->buildCustomMailMessage($this->resetUrl($notifiable), $notifiable);
    }

    /**
     * Get the reset password mail message for the given URL.
     *
     * @param  string  $url
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildCustomMailMessage($url, $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('[DartShop] Resetowanie hasła')
            ->line('Witaj ' . ($notifiable->name ?? 'Użytkowniku') . ',')
            ->line('Otrzymujesz ten email, ponieważ otrzymaliśmy żądanie resetowania hasła dla Twojego konta w **DartShop**.')
            ->action('🔑 Resetuj hasło', $url)
            ->line('**Ważne informacje:**')
            ->line('- Ten link do resetowania hasła wygaśnie za ' . config('auth.passwords.users.expire', 60) . ' minut')
            ->line('- Link jest jednorazowy - po użyciu stanie się nieaktywny')
            ->line('- Jeśli nie żądałeś resetowania hasła, po prostu zignoruj tę wiadomość')
            ->line('**Potrzebujesz pomocy?**')
            ->line('Jeśli masz pytania, skontaktuj się z nami pod adresem ' . config('mail.from.address'))
            ->salutation('Pozdrawienia, DartShop Team');
    }
} 