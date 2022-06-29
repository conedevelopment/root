<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Radio;
use Cone\Root\Tests\TestCase;

class RadioTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Radio('Role');
    }

    /** @test */
    public function a_radio_field_has_radio_type()
    {
        $this->assertSame('radio', $this->field->type);
    }

    /** @test */
    public function a_radio_field_has_radio_component()
    {
        $this->assertSame('Radio', $this->field->getComponent());
    }
}
