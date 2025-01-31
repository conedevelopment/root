<?php

namespace Cone\Root\Notifications;

use Cone\Root\Models\AuthCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class AuthCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The auth code instance.
     */
    protected AuthCode $code;

    /**
     * Create a new notification instance.
     */
    public function __construct(#[\SensitiveParameter] AuthCode $code)
    {
        $this->code = $code;
    }

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
        return (new MailMessage)
            ->subject(sprintf('%s - %s', Config::get('app.name'), __('Two Factor Code')))
            ->line(__('Your verification code is: :code.', ['code' => $this->code->code]))
            ->action(__('Verify Authentication'), URL::route('root.auth.two-factor.show', ['code' => $this->code->code]))
            ->line(__('The code expires at :date.', ['date' => $this->code->expires_at->format('Y-m-d H:i:s')]));
    }
}
