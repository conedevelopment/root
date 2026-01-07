<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Notifications;

use Cone\Root\Notifications\RootChannel;
use Cone\Root\Notifications\RootMessage;
use Cone\Root\Notifications\RootNotification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Notification;

final class RootChannelTest extends TestCase
{
    public function test_root_channel_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $notification = new TestRootNotification();

        (new RootChannel())->send($user, $notification);

        $this->assertDatabaseHas('root_notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'subject' => 'Test Subject',
            'message' => 'Test Message',
        ]);
    }

    public function test_root_channel_includes_notification_type(): void
    {
        $user = User::factory()->create();

        $notification = new TestRootNotification();

        (new RootChannel())->send($user, $notification);

        $this->assertDatabaseHas('root_notifications', [
            'type' => TestRootNotification::class,
        ]);
    }
}

class TestRootNotification extends RootNotification
{
    public function toRoot(object $notifiable): RootMessage
    {
        return (new RootMessage())
            ->subject('Test Subject')
            ->message('Test Message');
    }
}
