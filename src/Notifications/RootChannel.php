<?php

declare(strict_types=1);

namespace Cone\Root\Notifications;

use Illuminate\Notifications\Notification;

class RootChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $data = array_merge(
            ['type' => $notification::class],
            $notification->toRoot($notifiable)->toArray()
        );

        $notifiable->rootNotifications()->create($data);
    }
}
