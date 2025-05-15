<?php

namespace Cone\Root\Tests\Navigation;

use Cone\Root\Navigation\Item;
use Cone\Root\Navigation\Registry;
use Cone\Root\Tests\TestCase;

class NavigationTest extends TestCase
{
    protected Registry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry;
    }

    public function test_a_navigation_registry_can_register_locations(): void
    {
        $this->assertEmpty($this->registry->locations());

        $location = $this->registry->location('sidebar');

        $this->assertSame(['sidebar' => $location], $this->registry->locations());
    }

    public function test_a_navigation_location_has_items(): void
    {
        $location = $this->registry->location('sidebar');

        $item = $location->new('/dashboard', 'Dashboard', [], function (Item $item) {
            //
        });

        $this->assertSame(
            $item->toArray(),
            $location->get('/dashboard')->toArray()
        );

        $location->remove('/dashboard');

        $this->assertEmpty($location->all());
    }

    public function test_a_navigation_location_has_groups(): void
    {
        $location = $this->registry->location('sidebar');

        $location->new('/dashboard', 'Dashboard')->group('General');
        $location->new('/posts', 'Posts')->group('Blog');

        $this->assertSame(
            ['General', 'Blog'],
            array_keys($location->groups())
        );
    }

    public function test_a_navigation_item_has_items(): void
    {
        $location = $this->registry->location('sidebar');

        $item = $location->new('/posts', 'Posts');

        $child = $item->new('/posts/create', 'Create Post');

        $this->assertSame(
            $child->toArray(),
            $item->get('/posts/create')->toArray()
        );
    }
}
