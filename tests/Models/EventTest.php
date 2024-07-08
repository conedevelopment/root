<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Event;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class EventTest extends TestCase
{
    protected User $user;

    protected Event $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->event = Event::factory()->for($this->user)->for($this->user, 'target')->create();
    }

    public function test_an_event_belongs_to_a_user(): void
    {
        $this->assertTrue($this->event->user->is($this->user));
    }

    public function test_an_event_belongs_to_a_target(): void
    {
        $this->assertTrue($this->event->target->is($this->user));
    }
}
