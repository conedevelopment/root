<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Support;

use Cone\Root\Support\Copyable;
use Cone\Root\Tests\TestCase;

final class CopyableTest extends TestCase
{
    public function test_a_copyable_can_be_created(): void
    {
        $copyable = new Copyable('Display Text', 'Value to Copy');

        $this->assertInstanceOf(Copyable::class, $copyable);
    }

    public function test_a_copyable_can_be_made_with_same_text_and_value(): void
    {
        $copyable = Copyable::make('Test');

        $this->assertInstanceOf(Copyable::class, $copyable);
    }

    public function test_a_copyable_can_be_made_with_different_text_and_value(): void
    {
        $copyable = Copyable::make('Display', 'Copy Value');

        $this->assertInstanceOf(Copyable::class, $copyable);
    }

    public function test_a_copyable_can_be_rendered(): void
    {
        $copyable = Copyable::make('Test Text', 'Test Value');

        $rendered = $copyable->render();

        $this->assertStringContainsString('Test Text', $rendered);
        $this->assertStringContainsString('Test Value', $rendered);
    }

    public function test_a_copyable_can_be_converted_to_html(): void
    {
        $copyable = Copyable::make('Test Text', 'Test Value');

        $html = $copyable->toHtml();

        $this->assertStringContainsString('Test Text', $html);
        $this->assertStringContainsString('Test Value', $html);
    }

    public function test_a_copyable_can_be_converted_to_string(): void
    {
        $copyable = Copyable::make('Test Text', 'Test Value');

        $string = (string) $copyable;

        $this->assertStringContainsString('Test Text', $string);
        $this->assertStringContainsString('Test Value', $string);
    }
}
