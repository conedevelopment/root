<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Color;
use Cone\Root\Tests\TestCase;

final class ColorTest extends TestCase
{
    protected Color $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Color('Primary');
    }

    public function test_a_color_field_has_color_type(): void
    {
        $this->assertSame('color', $this->field->getAttribute('type'));
    }
}
