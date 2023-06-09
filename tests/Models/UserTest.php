<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Resources\Resource;
use Cone\Root\Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function a_user_has_uploads()
    {
        $medium = $this->admin->uploads()->save(
            Medium::factory()->make()
        );

        $this->assertTrue($this->admin->uploads->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function a_user_has_resource_representation()
    {
        $this->assertInstanceOf(Resource::class, $this->admin->toResource());
    }
}
