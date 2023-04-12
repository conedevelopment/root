<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Tests\Extracts\LongPosts;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ExtractControllerTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();

        $this->app->make('root')->routes(function ($router) {
            $router->group(
                ['prefix' => $this->resource->getKey().'/extracts', 'resource' => $this->resource->getKey()],
                function ($router) {
                    $this->extract->registerRoutes($router);
                }
            );
        });
    }

    /** @test */
    public function an_extract_controller_has_index()
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/extracts/long-posts'];
        });

        $this->extract->withQuery(function () {
            return Post::query();
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/extracts/long-posts')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Extracts/Index',
                'page.props' => function ($props) {
                    return empty(array_diff_key($this->extract->toIndex($this->app['request']), $props));
                },
            ]);
    }
}
