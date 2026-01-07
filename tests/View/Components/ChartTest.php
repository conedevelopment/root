<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Chart;

final class ChartTest extends TestCase
{
    public function test_chart_component_renders(): void
    {
        $component = new Chart;

        $view = $component->render();

        $this->assertSame('root::components.chart', $view->name());
        $this->assertSame([], $view->getData()['config']);
    }

    public function test_chart_component_accepts_config(): void
    {
        $config = [
            'type' => 'bar',
            'data' => ['values' => [1, 2, 3]],
        ];

        $component = new Chart($config);

        $view = $component->render();

        $this->assertSame($config, $view->getData()['config']);
    }
}
