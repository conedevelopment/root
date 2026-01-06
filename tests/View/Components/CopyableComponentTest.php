<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Copyable as CopyableComponent;

final class CopyableComponentTest extends TestCase
{
    public function test_copyable_component_renders(): void
    {
        $component = new CopyableComponent('Display Text', 'Copy Value');

        $view = $component->render();

        $this->assertSame('root::components.copyable', $view->name());
        $this->assertSame('Display Text', $view->getData()['text']);
        $this->assertSame('Copy Value', $view->getData()['value']);
    }
}
