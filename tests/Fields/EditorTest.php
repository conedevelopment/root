<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Editor;
use Cone\Root\Tests\TestCase;

class EditorTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Editor('Content');
    }

    /** @test */
    public function an_editor_field_has_defaults()
    {
        $this->assertSame('Editor', $this->field->getComponent());
    }
}
