<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Models\User;
use Cone\Root\Resources\Resource;
use Cone\Root\Tests\TestCase;

class UserTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_a_user_has_uploads()
    {
        $medium = $this->user->uploads()->save(
            Medium::factory()->make()
        );

        $this->assertTrue($this->user->uploads->contains($medium));
    }

    public function test_a_user_has_resource_representation()
    {
        $this->assertInstanceOf(Resource::class, $this->user->toResource());
    }
}
