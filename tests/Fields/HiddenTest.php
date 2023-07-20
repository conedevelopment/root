<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Form\Fields\Hidden;
use Cone\Root\Tests\TestCase;

class HiddenTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Hidden('Key');
    }

    /** @test */
    public function a_hidden_field_has_hidden_type()
    {
        $this->assertSame('hidden', $this->field->type);
    }
}
