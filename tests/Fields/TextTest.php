<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;

class TextTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Text('Title');
    }

    /** @test */
    public function a_text_field_has_defaults()
    {
        $this->assertSame('text', $this->field->type);
        $this->assertSame('Input', $this->field->getComponent());
    }

    /** @test */
    public function a_number_text_has_size_attribute()
    {
        $this->assertNull($this->field->size);

        $this->field->size(10);

        $this->assertSame(10, $this->field->size);
    }

    /** @test */
    public function a_number_text_has_minlength_attribute()
    {
        $this->assertNull($this->field->minlength);

        $this->field->minlength(10);

        $this->assertSame(10, $this->field->minlength);
    }

    /** @test */
    public function a_number_text_has_maxlength_attribute()
    {
        $this->assertNull($this->field->maxlength);

        $this->field->maxlength(10);

        $this->assertSame(10, $this->field->maxlength);
    }
}
