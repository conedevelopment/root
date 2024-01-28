<?php

namespace Cone\Root\Tests\Resources;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ResourceTest extends TestCase
{
    protected UserResource $resource;

    public function setUp(): void
    {
        parent::setUp();

        $this->resource = new UserResource();
    }

    public function test_a_resource_resolves_model(): void
    {
        $this->assertSame(User::class, $this->resource->getModel());
        $this->assertInstanceOf(User::class, $this->resource->getModelInstance());
    }

    public function test_a_resource_has_keys(): void
    {
        $this->assertSame('users', $this->resource->getKey());
        $this->assertSame('users', $this->resource->getUriKey());
        $this->assertSame('_resource', $this->resource->getRouteParameterName());
    }

    public function test_a_resource_has_names(): void
    {
        $this->assertSame('Users', $this->resource->getName());
        $this->assertSame('User', $this->resource->getModelName());
    }

    public function test_a_resource_has_icon(): void
    {
        $this->assertSame('archive', $this->resource->getIcon());
        $this->resource->icon('users');
        $this->assertSame('users', $this->resource->getIcon());
    }
}
