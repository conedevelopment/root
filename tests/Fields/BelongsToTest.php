<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\TestCase;

class BelongsToTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsTo('User');
    }

    /** @test */
    public function a_belongs_to_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_belongs_to_field_has_options()
    {
        $this->assertTrue(true);
    }
}
