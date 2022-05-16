<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Tests\LongPosts;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ExtractTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();

        $this->extract->withQuery(function () {
            return Post::query();
        });
    }

    /** @test */
    public function an_extract_registers_routes()
    {
        $this->app['router']->prefix('api/posts/extracts')->group(function ($router) {
            $this->extract->registerRoutes($this->request, $router);
        });

        $this->assertSame('api/posts/extracts/long-posts', $this->extract->getUri());

        $this->assertArrayHasKey(
            $this->extract->getUri(),
            $this->app['router']->getRoutes()->get('GET')
        );
    }

    /** @test */
    public function a_extract_has_fields()
    {
        $fields = $this->extract->resolveFields($this->request);

        $this->assertTrue($fields->contains(function ($field) {
            return $field->getKey() === 'title';
        }));
    }

    /** @test */
    public function a_extract_has_filters()
    {
        $filters = $this->extract->resolveFilters($this->request);

        $this->assertTrue($filters->contains(function ($field) {
            return $field->getKey() === 'type';
        }));
    }

    /** @test */
    public function a_extract_has_actions()
    {
        $actions = $this->extract->resolveActions($this->request);

        $this->assertTrue($actions->contains(function ($field) {
            return $field->getKey() === 'publish-posts';
        }));
    }

    /** @test */
    public function a_extract_has_widgets()
    {
        $widgets = $this->extract->resolveWidgets($this->request);

        $this->assertTrue($widgets->contains(function ($widget) {
            return $widget->getKey() === 'posts-count';
        }));
    }
}
