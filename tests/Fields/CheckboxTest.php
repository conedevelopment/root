<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Checkbox;
use Cone\Root\Tests\TestCase;

class CheckboxTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Checkbox('Permissions');
    }

    /** @test */
    public function a_checkbox_field_has_checkbox_type()
    {
        $this->assertSame('checkbox', $this->field->type);
    }

    /** @test */
    public function a_checkbox_field_has_checkbox_component()
    {
        $this->assertSame('Checkbox', $this->field->getComponent());
    }
}
