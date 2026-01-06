<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Icon;

final class IconTest extends TestCase
{
    public function test_icon_component_renders(): void
    {
        $component = new Icon('check');

        $view = $component->render();

        $this->assertSame('root::components.icon', $view->name());
        $this->assertSame('root::icons.check', $view->getData()['icon']);
    }

    public function test_icon_component_accepts_different_icons(): void
    {
        $component = new Icon('arrow-right');

        $view = $component->render();

        $this->assertSame('root::icons.arrow-right', $view->getData()['icon']);
    }
}
