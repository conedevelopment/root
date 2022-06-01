<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Tests\TestCase;

class ResourceTest extends TestCase
{
    /** @test */
    public function a_resource_has_icon()
    {
        $this->assertSame('inventory-2', $this->resource->getIcon());

        $this->resource->icon('fake-icon');

        $this->assertSame('fake-icon', $this->resource->getIcon());
    }
}
