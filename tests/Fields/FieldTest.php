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
    public function it_gets_attributes()
    {
        $this->assertSame('name', $this->field->name);
        $this->assertSame('name', $this->field->getAttribute('name'));

        $this->assertSame(
            ['label' => 'Name', 'name' => 'name', 'id' => 'name'],
            $this->field->getAttributes()
        );
    }
}
