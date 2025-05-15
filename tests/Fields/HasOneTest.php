<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasOne;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class HasOneTest extends TestCase
{
    protected HasOne $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new HasOne('Latest Upload', 'latestUpload');
    }

    public function test_a_has_one_field_hydates_model(): void
    {
        $user = User::factory()->create();

        $medium = Medium::factory()->create();

        $this->field->resolveHydrate($this->app['request'], $user, $medium->getKey());

        $this->assertTrue($user->latestUpload->is($medium));
    }
}
