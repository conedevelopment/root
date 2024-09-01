<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

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
        $medium = Medium::factory()->make();

        $user = new User;

        $this->field->resolveHydrate($this->app['request'], $medium, $user);

        $this->assertTrue($medium->user->is($user));
    }

    public function test_a_belongs_to_field_cannot_be_a_subresource(): void
    {
        $this->assertFalse($this->field->isSubResource());

        $this->field->asSubResource();

        $this->assertFalse($this->field->isSubResource());
    }
}
