<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Checkbox;
use Cone\Root\Tests\TestCase;

class CheckboxTest extends TestCase
{
    protected Checkbox $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Checkbox('Permissions');
    }

    public function test_a_checkbox_field_makes_new_option(): void
    {
        $option = $this->field->newOption('test', 'Test');

        $this->assertEmpty(
            array_diff(['value' => 'test'], $option->jsonSerialize())
        );
    }
}
