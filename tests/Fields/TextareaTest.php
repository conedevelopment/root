<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Textarea;
use Cone\Root\Tests\TestCase;

class TextareaTest extends TestCase
{
    protected Textarea $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Textarea('Content');
    }

    public function test_a_textarea_field_has_textarea_template(): void
    {
        $this->assertSame('root::fields.textarea', $this->field->getTemplate());
    }

    public function test_a_textarea_field_has_rows_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('rows'));

        $this->field->rows(10);

        $this->assertSame(10, $this->field->getAttribute('rows'));
    }

    public function test_a_textarea_field_has_cols_attribute(): void
    {
        $this->assertNull($this->field->getAttribute('cols'));

        $this->field->cols(10);

        $this->assertSame(10, $this->field->getAttribute('cols'));
    }
}
