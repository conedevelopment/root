<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Range;
use Cone\Root\Tests\TestCase;

class RangeTest extends TestCase
{
    protected Range $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Range('Age');
    }

    public function test_a_range_field_has_range_type(): void
    {
        $this->assertSame('range', $this->field->getAttribute('type'));
    }
}
