<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Models\Notification;
use Cone\Root\Tests\TestCase;

class NotificationsControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->admin->notifications()->saveMany(
            Notification::factory(10)->make()
        );
    }

    /** @test */
    public function a_notifications_controller_has_index()
    {
        $this->actingAs($this->admin)
            ->get('/root/api/notifications')
            ->assertOk()
            ->assertJson($this->admin->notifications()->paginate()->setPath('/root/api/notifications')->toArray());
    }

    /** @test */
    public function a_notifications_controller_has_show()
    {
        $notification = $this->admin->notifications->first();

        $this->actingAs($this->admin)
            ->get('/root/api/notifications/'.$notification->getKey())
            ->assertOk()
            ->assertJson($notification->toArray());
    }

    /** @test */
    public function a_notifications_controller_has_update()
    {
        $notification = $this->admin->notifications->first();

        $this->assertFalse($notification->read());

        $this->actingAs($this->admin)
            ->patch('/root/api/notifications/'.$notification->getKey())
            ->assertOk()
            ->assertJson($notification->refresh()->toArray());

        $this->assertTrue($notification->read());
    }

    /** @test */
    public function a_notifications_controller_has_destroy()
    {
        $notification = $this->admin->notifications->first();

        $this->actingAs($this->admin)
            ->delete('/root/api/notifications/'.$notification->getKey())
            ->assertOk()
            ->assertJson($notification->toArray());

        $this->assertDatabaseMissing('notifications', ['id' => $notification->getKey()]);
    }
}
