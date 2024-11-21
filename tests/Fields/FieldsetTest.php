<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Fieldset;
use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;

class FieldsetTest extends TestCase
{
    protected Fieldset $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = Fieldset::make('Properties')
            ->withFields(function () {
                return [
                    new Text('Name'),
                ];
            });
    }

    public function test_a_fieldset_field_has_fieldset_template(): void
    {
        $this->assertSame('root::fields.fieldset', $this->field->getTemplate());
    }
}
