<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Tests\PublishPosts;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\URL;

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
        $this->app['router']->prefix('api/posts/actions')->group(function ($router) {
            $this->action->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('api/posts/actions/publish-posts', $this->action->getUri());

        $this->post(URL::to($this->action->getUri()))
            ->assertRedirect('/posts');
    }

    /** @test */
    public function an_action_has_fields()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function an_action_has_form_representation()
    {
        $this->assertTrue(true);
    }
}
