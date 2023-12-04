<?php

namespace Cone\Root\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Support\Facades\URL;

class ResetPassword extends Notification
{
    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl(mixed $notifiable): string
    {
        return URL::route('root.auth.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
