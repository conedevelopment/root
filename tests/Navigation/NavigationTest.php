<?php

namespace Cone\Root\Tests\Navigation;

use Cone\Root\Navigation\Registry;
use Cone\Root\Tests\TestCase;

class NavigationTest extends TestCase
{
    protected Registry $registry;

    public function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry();
    }

    public function test_a_navigation_registry_can_register_locations(): void
    {
        $this->assertTrue(true);
    }
}
