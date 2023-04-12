<?php

namespace Cone\Root\Tests\Http\Controllers;

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

        $this->app->make('root')->routes(function ($router) {
            $router->group(
                ['prefix' => $this->resource->getKey().'/widgets', 'resource' => $this->resource->getKey()],
                function ($router) {
                    $this->widget->registerRoutes($router);
                }
            );
        });
    }

    /** @test */
    public function a_widget_controller_has_index()
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/widgets/posts-count'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/widgets/posts-count')
            ->assertOk();
    }
}
