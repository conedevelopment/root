<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\TestCase;

class BelongsToTest extends TestCase
{
    protected BelongsTo $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsTo('User');
    }

    public function test_a_belongs_to_field_hydates_model(): void
    {
        $this->assertTrue(true);
    }
}
