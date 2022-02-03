<?php

namespace Cone\Root\Tests\Actions;

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
            $this->extract->registerRoutes($this->app['request'], $router);
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
        $this->assertTrue(true);
    }

    /** @test */
    public function a_extract_has_filters()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_extract_has_actions()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_extract_has_widgets()
    {
        $this->assertTrue(true);
    }
}
