<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Resources\Resource;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ResourceTest extends TestCase
{
    protected $resource;

    public function setUp(): void
    {
        parent::setUp();

        $this->resource = new Resource(Post::class);
    }

    /** @test */
    public function a_resource_has_index_representation()
    {
        $request = IndexRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->getByName('root.posts.index');
        });

        $this->actingAs($this->admin)
            ->get('/root/posts')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Index',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->resource->toIndex($request), $props));
                },
            ]);
    }
}
