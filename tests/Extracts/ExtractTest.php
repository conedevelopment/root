<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Fields\Text;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Tests\Actions\PublishPosts;
use Cone\Root\Tests\Filters\Published;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\Widgets\PostsCount;

class ExtractTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();
    }

    /** @test */
    public function an_extract_has_key()
    {
        $this->assertSame('long-posts', $this->extract->getKey());
    }

    /** @test */
    public function an_extract_has_name()
    {
        $this->assertSame('Long Posts', $this->extract->getName());
    }

    /** @test */
    public function an_extract_resolvers_query()
    {
        $this->expectException(QueryResolutionException::class);

        try {
            $this->extract->resolveQuery($this->request);
        } finally {
            //
        }

        $this->extract->withQuery(function () {
            return Post::query();
        });

        $this->assertSame(
            Post::query()->toSql(),
            $this->extract->resolveQuery($this->request)->toSql()
        );
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
    public function an_extract_resolves_actions()
    {
        $this->extract->withActions([
            PublishPosts::make(),
        ]);

        $this->assertSame(
            Actions::make(array_merge($this->extract->actions($this->request), [PublishPosts::make()]))->toArray(),
            $this->extract->resolveActions($this->request)->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_filters()
    {
        $this->extract->withFilters([
            Published::make(),
        ]);

        $this->assertSame(
            Filters::make(array_merge($this->extract->filters($this->request), [Published::make()]))->toArray(),
            $this->extract->resolveFilters($this->request)->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_fields()
    {
        $this->extract->withFields([
            Text::make(__('Name')),
        ]);

        $this->assertSame(
            Fields::make(array_merge($this->extract->fields($this->request), [Text::make(__('Name'))]))->toArray(),
            $this->extract->resolveFields($this->request)->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_widgets()
    {
        $this->extract->withWidgets([
            PostsCount::make(),
        ]);

        $this->assertSame(
            Widgets::make(array_merge($this->extract->widgets($this->request), [PostsCount::make()]))->toArray(),
            $this->extract->resolveWidgets($this->request)->toArray()
        );
    }

    /** @test */
    public function an_extract_has_array_representation()
    {
        $this->assertSame([
            'key' => $this->extract->getKey(),
            'name' => $this->extract->getName(),
            'url' => $this->app['url']->to($this->extract->getUri()),
        ], $this->extract->toArray());
    }
}
