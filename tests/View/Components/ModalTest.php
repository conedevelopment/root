<?php

declare(strict_types=1);

namespace Cone\Root\Tests\View\Components;

use Cone\Root\Tests\TestCase;
use Cone\Root\View\Components\Modal;

final class ModalTest extends TestCase
{
    public function test_modal_component_renders_with_title(): void
    {
        $component = new Modal('Test Title');

        $view = $component->render();

        $this->assertSame('root::components.modal', $view->name());
        $this->assertSame('Test Title', $view->getData()['title']);
    }

    public function test_modal_component_accepts_subtitle(): void
    {
        $component = new Modal('Test Title', 'Test Subtitle');

        $view = $component->render();

        $this->assertSame('Test Subtitle', $view->getData()['subtitle']);
    }

    public function test_modal_component_generates_key_when_not_provided(): void
    {
        $component = new Modal('Test Title');

        $view = $component->render();

        $this->assertNotEmpty($view->getData()['key']);
        $this->assertIsString($view->getData()['key']);
    }

    public function test_modal_component_uses_provided_key(): void
    {
        $component = new Modal('Test Title', null, 'custom-key');

        $view = $component->render();

        $this->assertSame('custom-key', $view->getData()['key']);
    }

    public function test_modal_component_normalizes_key_to_lowercase(): void
    {
        $component = new Modal('Test Title', null, 'CUSTOM-KEY');

        $view = $component->render();

        $this->assertSame('custom-key', $view->getData()['key']);
    }

    public function test_modal_component_defaults_to_closed(): void
    {
        $component = new Modal('Test Title');

        $view = $component->render();

        $this->assertFalse($view->getData()['open']);
    }

    public function test_modal_component_can_be_opened(): void
    {
        $component = new Modal('Test Title', null, null, true);

        $view = $component->render();

        $this->assertTrue($view->getData()['open']);
    }
}
