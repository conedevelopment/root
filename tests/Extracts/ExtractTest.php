<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Form\Fields;
use Cone\Root\Form\Fields\Text;
use Cone\Root\Support\Collections\Actions;
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
    public function an_extract_throws_exception_if_cant_resolve_query()
    {
        $this->expectException(QueryResolutionException::class);

        $this->extract->resolveQuery($this->app['request']);
    }

    /** @test */
    public function an_extract_resolvers_query()
    {
        $this->extract->withQuery(function () {
            return Post::query();
        });

        $this->assertSame(
            Post::query()->toSql(),
            $this->extract->resolveQuery($this->app['request'])->toSql()
        );
    }

    /** @test */
    public function an_extract_registers_routes()
    {
        $this->app['router']->prefix('posts/extracts')->group(function ($router) {
            $this->extract->registerRoutes($router);
        });

        $this->assertSame('/posts/extracts/long-posts', $this->extract->getUri());

        $this->assertArrayHasKey(
            trim($this->extract->getUri(), '/'),
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
            Actions::make(array_merge($this->extract->actions($this->app['request']), [PublishPosts::make()]))->toArray(),
            $this->extract->resolveActions($this->app['request'])->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_filters()
    {
        $this->extract->withFilters([
            Published::make(),
        ]);

        $this->assertSame(
            Filters::make(array_merge($this->extract->filters($this->app['request']), [Published::make()]))->toArray(),
            $this->extract->resolveFilters($this->app['request'])->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_fields()
    {
        $this->extract->withFields([
            Text::make(__('Name')),
        ]);

        $this->assertSame(
            Fields::make(array_merge($this->extract->fields($this->app['request']), [Text::make(__('Name'))]))->toArray(),
            $this->extract->resolveFields($this->app['request'])->toArray()
        );
    }

    /** @test */
    public function an_extract_resolves_widgets()
    {
        $this->extract->withWidgets([
            PostsCount::make(),
        ]);

        $this->assertSame(
            Widgets::make(array_merge($this->extract->widgets($this->app['request']), [PostsCount::make()]))->toArray(),
            $this->extract->resolveWidgets($this->app['request'])->toArray()
        );
    }

    /** @test */
    public function an_extract_has_array_representation()
    {
        $this->assertSame([
            'key' => $this->extract->getKey(),
            'name' => $this->extract->getName(),
            'url' => $this->extract->getUri(),
        ], $this->extract->toArray());
    }
}
