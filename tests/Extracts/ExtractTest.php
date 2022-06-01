<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ExtractTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('extracts')->group(function ($router) {
                $this->extract->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function an_extract_has_index_representation()
    {
        $request = ExtractRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
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
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->extract->toIndex($request), $props));
                },
            ]);
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->extract->resolveQuery($this->request);
    }
}
