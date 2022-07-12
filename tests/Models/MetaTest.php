<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Models\Meta;
use Cone\Root\Tests\TestCase;

class MetaTest extends TestCase
{
    /** @test */
    public function a_meta_belongs_to_a_metable()
    {
        $meta = Meta::factory()->make();

        $medium = Medium::factory()->create();

        $meta->metable()->associate($medium)->save();

        $this->assertSame(
            [Medium::class, $medium->id],
            [$meta->metable_type, $meta->metable_id]
        );
    }
}
