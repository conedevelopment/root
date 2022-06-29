<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Range;
use Cone\Root\Tests\TestCase;

class RangeTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Range('Age');
    }

    /** @test */
    public function a_range_field_has_range_type()
    {
        $this->assertSame('range', $this->field->type);
    }
}
