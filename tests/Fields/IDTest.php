<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\ID;
use Cone\Root\Tests\TestCase;

final class IDTest extends TestCase
{
    protected ID $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new ID;
    }

    public function test_an_id_field_has_default_attributes(): void
    {
        $this->assertSame('ID', $this->field->getLabel());
        $this->assertSame('id', $this->field->getAttribute('name'));
    }
}
