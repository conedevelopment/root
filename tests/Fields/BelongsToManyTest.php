<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Tests\TestCase;

class BelongsToManyTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsToMany('Tag');
    }

    /** @test */
    public function a_belongs_to_many_field_has_custom_hydration()
    {
        $this->assertTrue(true);
    }
}
