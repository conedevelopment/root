<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasMany;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

final class HasManyTest extends TestCase
{
    protected HasMany $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Uploads');
    }

    public function test_a_has_many_field_hydates_model(): void
    {
        $user = User::factory()->create();

        $medium = Medium::factory()->create();

        $this->field->resolveHydrate($this->app['request'], $user, [$medium->getKey()]);

        $this->assertTrue($user->uploads->contains($medium));
    }
}
