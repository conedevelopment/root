<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Select;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\TestCase;

class SelectTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Select('Status');
    }

    /** @test */
    public function a_select_field_has_select_component()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_select_field_can_be_nullable()
    {
        $this->assertFalse($this->field->isNullable());

        $this->field->nullable();

        $this->assertTrue($this->field->isNullable());
    }

    /** @test */
    public function a_select_field_can_be_multiple()
    {
        $this->assertNull($this->field->multiple);

        $this->field->multiple();

        $this->assertTrue($this->field->multiple);
    }

    /** @test */
    public function a_select_field_has_options()
    {
        $model = new Author();

        $this->assertEmpty($this->field->resolveOptions($this->app['request'], $model));

        $this->field->options(['key' => 'value']);

        $this->assertSame(
            [['value' => 'key', 'formatted_value' => 'value']],
            $this->field->resolveOptions($this->app['request'], $model)
        );
    }
}
