<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Tests\Actions\PublishPosts;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ActionControllerTest extends TestCase
{
    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishPosts();

        $this->app->make('root')->routes(function ($router) {
            $router->group(
                ['prefix' => $this->resource->getKey().'/actions', 'resource' => $this->resource->getKey()],
                function ($router) {
                    $this->action->registerRoutes($this->request, $router);
                }
            );
        });
    }

    /** @test */
    public function an_action_controller_has_index()
    {
        $request = ActionRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/actions/publish-posts'];
        });

        $this->action->withQuery(function () {
            return Post::query();
        });

        $this->actingAs($this->admin)
            ->post('/root/posts/actions/publish-posts')
            ->assertRedirect()
            ->assertSessionHas('alerts.action-publish-posts');
    }
}
