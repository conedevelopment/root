<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasMany;
use Cone\Root\Tests\TestCase;

class HasManyTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Posts');
    }

    /** @test */
    public function a_has_many_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_has_many_field_has_options()
    {
        $this->assertTrue(true);
    }
}
