<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Notification;
use Cone\Root\Tests\TestCase;

class NotificationTest extends TestCase
{
    protected $notification;

    public function setUp(): void
    {
        parent::setUp();

        $this->notification = Notification::factory()->make();

        $this->notification->notifiable()->associate($this->admin);

        $this->notification->save();
    }

    /** @test */
    public function a_notification_belongs_to_a_notifiable()
    {
        $this->assertTrue($this->notification->notifiable->is($this->admin));
    }
}
