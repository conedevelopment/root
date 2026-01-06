<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Alert as AlertComponent;

final class AlertComponentTest extends TestCase
{
    public function test_alert_component_can_be_instantiated(): void
    {
        $component = new AlertComponent();

        $this->assertInstanceOf(AlertComponent::class, $component);
    }

    public function test_alert_component_renders(): void
    {
        $component = new AlertComponent();

        $view = $component->render();

        $this->assertSame('root::components.alert', $view->name());
    }
}
