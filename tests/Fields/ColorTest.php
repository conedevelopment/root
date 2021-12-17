<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Color;
use Cone\Root\Tests\TestCase;

class ColorTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Color('Primary');
    }

    /** @test */
    public function a_color_field_has_defaults()
    {
        $this->assertSame('color', $this->field->type);
        $this->assertSame('Input', $this->field->getComponent());
    }
}
