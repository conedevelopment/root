<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;

class TextTest extends TestCase
{
    protected Text $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Text('Name');
    }

    public function test_a_text_field_has_text_type(): void
    {
        $this->assertSame('text', $this->field->getAttribute('type'));
    }

    public function test_a_text_field_has_size_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('size'));

        $this->field->size(10);

        $this->assertSame(10, $this->field->getAttribute('size'));
    }

    public function test_a_text_field_has_minlength_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('minlength'));

        $this->field->minlength(10);

        $this->assertSame(10, $this->field->getAttribute('minlength'));
    }

    public function test_a_text_field_has_maxlength_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('maxlength'));

        $this->field->maxlength(10);

        $this->assertSame(10, $this->field->getAttribute('maxlength'));
    }
}
