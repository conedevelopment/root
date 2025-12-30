<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Dropdown;
use Cone\Root\Tests\TestCase;

final class DropdownTest extends TestCase
{
    protected Dropdown $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = Dropdown::make('Sizes')
            ->options([
                's' => 'S',
                'm' => 'M',
                'l' => 'L',
            ]);
    }

    public function test_a_fieldset_field_has_fieldset_template(): void
    {
        $this->assertSame('root::fields.dropdown', $this->field->getTemplate());
    }
}
