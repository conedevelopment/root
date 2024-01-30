<?php

namespace Cone\Root\Notifications;

use Illuminate\Notifications\Notification;

class RootChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $data = $notification->toRoot($notifiable)->toArray();

        $notifiable->rootNotifications()->create($data);
    }
}
