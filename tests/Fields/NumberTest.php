<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Number;
use Cone\Root\Tests\TestCase;

final class NumberTest extends TestCase
{
    protected Number $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Number('Age');
    }

    public function test_a_number_field_has_number_type(): void
    {
        $this->assertSame('number', $this->field->getAttribute('type'));
    }

    public function test_a_number_field_has_min_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('min'));

        $this->field->min(10);

        $this->assertSame(10, $this->field->getAttribute('min'));
    }

    public function test_a_number_field_has_max_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('max'));

        $this->field->max(10);

        $this->assertSame(10, $this->field->getAttribute('max'));
    }

    public function test_a_number_field_has_step_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('step'));

        $this->field->step(1);

        $this->assertSame(1, $this->field->getAttribute('step'));
    }
}
