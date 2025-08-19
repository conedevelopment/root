<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Radio;
use Cone\Root\Tests\TestCase;

class RadioTest extends TestCase
{
    protected Radio $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Radio('Role');
    }

    public function test_a_checkbox_field_makes_new_option(): void
    {
        $option = $this->field->newOption('test', 'Test');

        $this->assertEmpty(
            array_diff(['value' => 'test'], $option->jsonSerialize())
        );
    }
}
