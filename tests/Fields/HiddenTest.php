<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Hidden;
use Cone\Root\Tests\TestCase;

class HiddenTest extends TestCase
{
    protected Hidden $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Hidden('Token');
    }

    public function test_a_hidden_field_has_hidden_type(): void
    {
        $this->assertSame('hidden', $this->field->getAttribute('type'));
    }
}
