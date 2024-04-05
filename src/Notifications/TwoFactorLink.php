<?php

namespace Cone\Root\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class TwoFactorLink extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $url = URL::temporarySignedRoute(
            'root.auth.two-factor.verify',
            Config::get('root.two_factor.expiration', 600),
            ['hash' => sha1($notifiable->email)]
        );

        return (new MailMessage())
            ->subject(__('Two Factor Authentication Link'))
            ->line(__('To finish the two factor authentication process, please click the link below.'))
            ->action(__('Finish Two Factor Login'), $url);
    }
}
