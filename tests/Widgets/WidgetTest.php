<?php

namespace Cone\Root\Tests\Widgets;

use Cone\Root\Tests\TestCase;
use Cone\Root\Widgets\Welcome;
use Cone\Root\Widgets\Widget;

class WidgetTest extends TestCase
{
    protected Widget $widget;

    protected function setUp(): void
    {
        parent::setUp();

        $this->widget = new class extends Welcome
        {
            protected bool $async = true;

            public function getKey(): string
            {
                return 'welcome';
            }
        };
    }

    public function test_a_widget_registers_routes(): void
    {
        $this->app['router']->prefix('/root/widgets')->group(function ($router) {
            $this->widget->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('/root/widgets/welcome', $this->widget->getUri());

        $this->assertArrayHasKey(
            trim($this->widget->getUri(), '/'),
            $this->app['router']->getRoutes()->get('GET')
        );
    }
}
