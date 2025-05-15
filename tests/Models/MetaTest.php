<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Meta;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class MetaTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_a_meta_belongs_to_a_metable(): void
    {
        $meta = Meta::factory()->make();

        $meta->metable()->associate($this->user)->save();

        $this->assertTrue($meta->metable->is($this->user));
    }

    public function test_a_metable_model_has_meta_data(): void
    {
        $this->user->metaData()->save(
            $meta = Meta::factory()->make()
        );

        $this->assertTrue($this->user->metaData->contains($meta));
    }
}
