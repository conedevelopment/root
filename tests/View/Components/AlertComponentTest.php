<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Alert as AlertComponent;

final class AlertComponentTest extends TestCase
{
    public function test_alert_component_can_be_instantiated(): void
    {
        $component = new AlertComponent;

        $this->assertInstanceOf(AlertComponent::class, $component);
    }

    public function test_alert_component_renders(): void
    {
        $component = new AlertComponent;

        $view = $component->render();

        $this->assertSame('root::components.alert', $view->name());
        $this->assertSame('info', $view->getData()['type']);
        $this->assertFalse($view->getData()['closable']);
    }

    public function test_alert_component_accepts_type(): void
    {
        $component = new AlertComponent('success');

        $view = $component->render();

        $this->assertSame('success', $view->getData()['type']);
        $this->assertSame('alert--success', $view->getData()['class']);
    }

    public function test_alert_component_handles_error_type(): void
    {
        $component = new AlertComponent('error');

        $view = $component->render();

        $this->assertSame('error', $view->getData()['type']);
        $this->assertSame('alert--danger', $view->getData()['class']);
    }

    public function test_alert_component_can_be_closable(): void
    {
        $component = new AlertComponent('info', true);

        $view = $component->render();

        $this->assertTrue($view->getData()['closable']);
    }
}
