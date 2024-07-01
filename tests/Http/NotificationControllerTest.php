<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Models\Notification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class NotificationControllerTest extends TestCase
{
    protected User $admin;

    protected Notification $notification;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->notification = Notification::factory()->unread()->for($this->admin, 'notifiable')->create();
    }

    public function test_notification_controller_handles_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/api/notifications')
            ->assertOk()
            ->assertJson($this->admin->rootNotifications()->paginate()->toArray());
    }

    public function test_notification_controller_handles_show(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/api/notifications/'.$this->notification->getKey())
            ->assertOk()
            ->assertJson($this->notification->toArray());
    }

    public function test_notification_controller_handles_update(): void
    {
        $this->assertTrue($this->notification->unread());

        $this->actingAs($this->admin)
            ->patch('/root/api/notifications/'.$this->notification->getKey())
            ->assertOk()
            ->assertJson($this->notification->refresh()->toArray());

        $this->assertTrue($this->notification->read());
    }

    public function test_notification_controller_handles_destroy(): void
    {
        $this->actingAs($this->admin)
            ->delete('/root/api/notifications/'.$this->notification->getKey())
            ->assertOk()
            ->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('root_notifications', ['id' => $this->notification->getKey()]);
    }
}
