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
    public function an_action_has_key()
    {
        $this->assertSame('publish-posts', $this->action->getKey());
    }

    /** @test */
    public function an_action_has_name()
    {
        $this->assertSame('Publish Posts', $this->action->getName());
    }

    /** @test */
    public function an_action_can_be_destructive()
    {
        $this->assertFalse($this->action->isDestructive());

        $this->action->destructive();

        $this->assertTrue($this->action->isDestructive());

        $this->action->destructive(false);

        $this->assertFalse($this->action->isDestructive());
    }

    /** @test */
    public function an_action_can_be_confirmable()
    {
        $this->assertFalse($this->action->isConfirmable());

        $this->action->confirmable();

        $this->assertTrue($this->action->isConfirmable());

        $this->action->confirmable(false);

        $this->assertFalse($this->action->isConfirmable());
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->action->resolveQuery($this->request);
    }

    /** @test */
    public function an_action_registers_routes()
    {
        $this->app['router']->prefix('posts/actions')->group(function ($router) {
            $this->action->registerRoutes($this->request, $router);
        });

        $this->assertSame('posts/actions/publish-posts', $this->action->getUri());

        $this->assertArrayHasKey(
            $this->action->getUri(),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    /** @test */
    public function an_action_resolves_fields()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_has_array_representation()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_has_form_representation()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_has_response_representation()
    {
        $this->assertTrue(true);
    }
}
