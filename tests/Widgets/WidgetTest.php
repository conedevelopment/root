<?php

namespace Cone\Root\Tests\Widgets;

use Cone\Root\Tests\TestCase;

class WidgetTest extends TestCase
{
    protected $widget;

    public function setUp(): void
    {
        parent::setUp();

        $this->widget = new PostsCount();
    }

    /** @test */
    public function a_widget_has_key()
    {
        $this->assertSame('posts-count', $this->widget->getKey());
    }

    /** @test */
    public function a_widget_has_name()
    {
        $this->assertSame('Posts Count', $this->widget->getName());
    }

    /** @test */
    public function a_widget_has_component()
    {
        $this->assertSame('Widget', $this->widget->getComponent());
    }

    /** @test */
    public function a_widget_has_template()
    {
        $this->assertSame('root::widgets.welcome', $this->widget->getTemplate());
    }

    /** @test */
    public function a_widget_can_be_async()
    {
        $this->assertFalse($this->widget->isAsync());

        $this->widget->async();

        $this->assertTrue($this->widget->isAsync());
    }

    /** @test */
    public function a_widget_resolves_data()
    {
        $this->widget->with(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $this->widget->resolveData($this->request));
    }

    /** @test */
    public function a_widget_is_renderable()
    {
        $this->assertSame(
            $this->app['view']->make($this->widget->getTemplate())->render(),
            $this->widget->render()
        );
    }

    /** @test */
    public function an_async_widget_registers_routes()
    {
        $this->widget->async();

        $this->app['router']->prefix('posts/widgets')->group(function ($router) {
            $this->widget->registerRoutes($this->request, $router);
        });

        $this->assertSame('/posts/widgets/posts-count', $this->widget->getUri());

        $this->assertArrayHasKey(
            trim($this->widget->getUri(), '/'),
            $this->app['router']->getRoutes()->get('GET')
        );
    }
}
