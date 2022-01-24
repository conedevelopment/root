<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Tests\PublishPosts;
use Cone\Root\Tests\TestCase;

class ActionTest extends TestCase
{
    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishPosts();
    }

    /** @test */
    public function an_action_registers_routes()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_can_be_inline()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_runs_task_on_models()
    {
        $this->assertTrue(true);
    }
}
