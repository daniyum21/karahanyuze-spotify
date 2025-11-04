<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyEmailNotification extends Notification
{
    use Queueable;
    
    // Force synchronous sending (not queued)
    public $shouldQueue = false;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Ensure we're using SMTP, not log
        $mailer = config('mail.default');
        if ($mailer === 'log' || $mailer === 'file') {
            Log::warning('Email notification attempted but mailer is set to log/file. Please set MAIL_MAILER=smtp in .env', [
                'mailer' => $mailer,
                'user_email' => $notifiable->getEmailForVerification()
            ]);
        }
        
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        Log::info('Building verification email', [
            'user_id' => $notifiable->UserID ?? null,
            'email' => $notifiable->getEmailForVerification(),
            'verification_url' => $verificationUrl,
        ]);

        return (new MailMessage)
            ->subject('Verify Your Email Address - Karahanyuze')
            ->greeting('Hello ' . ($notifiable->FirstName ?? $notifiable->UserName) . '!')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Thank you for using Karahanyuze!');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
