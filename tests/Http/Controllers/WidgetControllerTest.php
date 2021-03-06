<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Http\Requests\WidgetRequest;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\Widgets\PostsCount;

class WidgetControllerTest extends TestCase
{
    protected $widget;

    public function setUp(): void
    {
        parent::setUp();

        $this->widget = new PostsCount();

        $this->widget->async();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('widgets')->group(function ($router) {
                $this->widget->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function a_widget_controller_has_index()
    {
        $request = WidgetRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/widgets/posts-count'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/widgets/posts-count')
            ->assertOk();
    }
}
