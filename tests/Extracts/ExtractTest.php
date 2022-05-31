<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Tests\LongPosts;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;
use Illuminate\Routing\Route;

class ExtractTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();
    }

    /** @test */
    public function an_extract_registers_routes()
    {
        $this->app['router']->prefix('posts/extracts')->group(function ($router) {
            $this->extract->registerRoutes($this->request, $router);
        });

        $this->assertSame('posts/extracts/long-posts', $this->extract->getUri());

        $this->assertArrayHasKey(
            $this->extract->getUri(),
            $this->app['router']->getRoutes()->get('GET')
        );
    }

    /** @test */
    public function an_extract_has_fields()
    {
        $fields = $this->extract->resolveFields($this->request);

        $this->assertTrue($fields->contains(function ($field) {
            return $field->getKey() === 'title';
        }));
    }

    /** @test */
    public function an_extract_has_filters()
    {
        $filters = $this->extract->resolveFilters($this->request);

        $this->assertTrue($filters->contains(function ($field) {
            return $field->getKey() === 'type';
        }));
    }

    /** @test */
    public function an_extract_has_actions()
    {
        $actions = $this->extract->resolveActions($this->request);

        $this->assertTrue($actions->contains(function ($field) {
            return $field->getKey() === 'publish-posts';
        }));
    }

    /** @test */
    public function an_extract_has_widgets()
    {
        $widgets = $this->extract->resolveWidgets($this->request);

        $this->assertTrue($widgets->contains(function ($widget) {
            return $widget->getKey() === 'posts-count';
        }));
    }

    /** @test */
    public function an_extract_has_index_representation()
    {
        $request = ExtractRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return new Route('GET', '/', ['resource' => 'posts']);
        });

        $this->extract->withQuery(function () {
            return Post::query();
        });

        $this->assertSame([
            'actions' => $this->extract->resolveActions($request)->available($request)->toArray(),
            'extract' => $this->extract->toArray(),
            'filters' => $this->extract->resolveFilters($request)->available($request)->mapToForm($request)->toArray(),
            'items' => $this->extract->mapItems($request),
            'resource' => $request->resource()->toArray(),
            'title' => $this->extract->getName(),
            'widgets' => $this->extract->resolveWidgets($request)->available($request)->toArray(),

        ], $this->extract->toIndex($request));
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->extract->resolveQuery($this->request);
    }
}
