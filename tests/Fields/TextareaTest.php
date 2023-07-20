<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Form\Fields\Textarea;
use Cone\Root\Tests\TestCase;

class TextareaTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Textarea('Content');
    }

    /** @test */
    public function a_text_field_has_textarea_component()
    {
        $this->assertSame('Textarea', $this->field->getComponent());
    }

    /** @test */
    public function a_number_textarea_has_rows_attribute()
    {
        $this->assertNull($this->field->rows);

        $this->field->rows(10);

        $this->assertSame(10, $this->field->rows);
    }

    /** @test */
    public function a_number_textarea_has_cols_attribute()
    {
        $this->assertNull($this->field->cols);

        $this->field->cols(10);

        $this->assertSame(10, $this->field->cols);
    }
}
