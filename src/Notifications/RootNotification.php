<?php

declare(strict_types=1);

namespace Cone\Root\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class RootNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [RootChannel::class];
    }

    /**
     * Get the Root Message representation of the notification.
     */
    abstract public function toRoot(object $notifiable): RootMessage;
}
