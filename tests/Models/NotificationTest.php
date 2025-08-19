<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Notification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class NotificationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_a_notification_belongs_to_a_notifiable(): void
    {
        $notification = Notification::factory()->make();

        $notification->notifiable()->associate($this->user);

        $notification->save();

        $this->assertTrue($notification->notifiable->is($this->user));
    }
}
