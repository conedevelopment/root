<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\ID;
use Cone\Root\Tests\TestCase;

class IDTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new ID();
    }

    /** @test */
    public function an_id_field_can_be_uuid()
    {
        $this->assertFalse($this->field->isUuid());

        $this->field->uuid();

        $this->assertTrue($this->field->isUuid());
    }
}
