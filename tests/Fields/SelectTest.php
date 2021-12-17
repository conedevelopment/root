<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Select;
use Cone\Root\Models\User;
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
    public function a_select_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_select_field_has_options()
    {
        $this->assertEmpty($this->field->resolveOptions($this->app['request'], new User()));

        $this->field->options(['key' => 'value']);

        $this->assertSame(
            ['key' => 'value'],
            $this->field->resolveOptions($this->app['request'], new User())
        );
    }
}
