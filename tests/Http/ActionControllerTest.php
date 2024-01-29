<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ActionControllerTest extends TestCase
{
    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_an_action_controller_handles_action_request(): void
    {
        $this->actingAs($this->admin)
            ->post('/root/users/actions/send-password-reset-notification')
            ->assertRedirect()
            ->assertSessionHas('alerts.action-send-password-reset-notification');
    }
}
