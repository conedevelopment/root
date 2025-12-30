<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Breadcrumbs;

use Cone\Root\Breadcrumbs\Registry;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

final class RegistryTest extends TestCase
{
    protected Registry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new Registry;
    }

    public function test_a_breadcrumb_registry_can_register_patterns(): void
    {
        $this->registry->patterns([
            '/' => 'Dashboard',
            '/posts' => 'Posts',
            '/posts/{post}' => function () {
                return 'Post Title';
            },
            '/settings' => 'Settings',
        ]);

        $request = Request::create('/posts/1');

        $route = new Route('GET', '/posts/{post}', fn () => null);
        $route->bind($request);
        $route->setParameter('post', 1);

        $request->setRouteResolver(fn () => $route);

        $this->assertSame([
            ['uri' => '/', 'label' => 'Dashboard'],
            ['uri' => '/posts', 'label' => 'Posts'],
            ['uri' => '/posts/1', 'label' => 'Post Title'],
        ], $this->registry->resolve($request));
    }
}
