<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Field;
use Cone\Root\Tests\TestCase;

class FieldTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Field('Name');
    }

    /** @test */
    public function a_field_gets_attributes()
    {
        $this->assertSame('name', $this->field->name);
        $this->assertSame('name', $this->field->getAttribute('name'));

        $this->assertSame(
            ['label' => 'Name', 'name' => 'name', 'id' => 'name'],
            $this->field->getAttributes()
        );
    }

    /** @test */
    public function a_field_checks_attributes()
    {
        $this->assertTrue(isset($this->field->name));
        $this->assertTrue($this->field->hasAttribute('name'));

        $this->assertFalse(isset($this->field->foo));
        $this->assertFalse($this->field->hasAttribute('foo'));
    }

    /** @test */
    public function a_field_sets_attributes()
    {
        $this->assertFalse($this->field->hasAttribute('min'));
        $this->assertFalse($this->field->hasAttribute('max'));

        $this->field->min = 10;
        $this->field->setAttribute('max', 30);

        $this->assertTrue($this->field->hasAttribute('min'));
        $this->assertTrue($this->field->hasAttribute('max'));

        $this->assertSame(10, $this->field->getAttribute('min'));
        $this->assertSame(30, $this->field->getAttribute('max'));
    }

    /** @test */
    public function a_feild_removes_attributes()
    {
        $this->field->setAttribute('max', 30);

        $this->assertTrue($this->field->hasAttribute('max'));

        $this->field->removeAttribute('max');

        $this->assertFalse($this->field->hasAttribute('max'));
    }

    /** @test */
    public function a_field_clears_attributes()
    {
        $this->assertNotEmpty($this->field->getAttributes());

        $this->field->clearAttributes();

        $this->assertEmpty($this->field->getAttributes());
    }

    /** @test */
    public function a_field_handles_label()
    {
        $this->field->label('Test');

        $this->assertSame('Test', $this->field->label);
    }

    /** @test */
    public function a_field_handles_name()
    {
        $this->field->name('test');

        $this->assertSame('test', $this->field->name);
    }

    /** @test */
    public function a_field_handles_id()
    {
        $this->field->id('test');

        $this->assertSame('test', $this->field->id);
    }

    /** @test */
    public function a_field_handles_readonly()
    {
        $this->field->readonly();

        $this->assertTrue($this->field->readonly);

        $this->field->readonly(false);

        $this->assertFalse($this->field->readonly);
    }

    /** @test */
    public function a_field_handles_disabled()
    {
        $this->field->disabled();

        $this->assertTrue($this->field->disabled);

        $this->field->disabled(false);

        $this->assertFalse($this->field->disabled);
    }

    /** @test */
    public function a_field_handles_required()
    {
        $this->field->required();

        $this->assertTrue($this->field->required);

        $this->field->required(false);

        $this->assertFalse($this->field->required);
    }
}
