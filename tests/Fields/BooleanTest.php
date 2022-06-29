<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Boolean;
use Cone\Root\Tests\TestCase;

class BooleanTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Boolean('Admin');
    }

    /** @test */
    public function a_boolean_field_has_checkbox_type()
    {
        $this->assertSame('checkbox', $this->field->type);
    }

    /** @test */
    public function a_boolean_field_has_checkbox_component()
    {
        $this->assertSame('Checkbox', $this->field->getComponent());
    }
}
