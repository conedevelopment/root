<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Number;
use Cone\Root\Tests\TestCase;

class NumberTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Number('Age');
    }

    /** @test */
    public function a_number_field_has_number_type()
    {
        $this->assertSame('number', $this->field->type);
    }

    /** @test */
    public function a_number_field_has_min_attribute()
    {
        $this->assertNull($this->field->min);

        $this->field->min(10);

        $this->assertSame(10, $this->field->min);
    }

    /** @test */
    public function a_number_field_has_max_attribute()
    {
        $this->assertNull($this->field->max);

        $this->field->max(10);

        $this->assertSame(10, $this->field->max);
    }
}
