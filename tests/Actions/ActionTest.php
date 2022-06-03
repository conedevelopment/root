<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Exceptions\QueryResolutionException;
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
    public function an_action_can_be_destructive()
    {
        $this->assertFalse($this->action->toArray()['destructive']);

        $this->action->destructive();

        $this->assertTrue($this->action->toArray()['destructive']);

        $this->action->destructive(false);

        $this->assertFalse($this->action->toArray()['destructive']);
    }

    /** @test */
    public function an_action_can_be_confirmable()
    {
        $this->assertFalse($this->action->toArray()['confirmable']);

        $this->action->confirmable();

        $this->assertTrue($this->action->toArray()['confirmable']);

        $this->action->confirmable(false);

        $this->assertFalse($this->action->toArray()['confirmable']);
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->action->resolveQuery($this->request);
    }
}
