<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Resources;

use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ResourceTest extends TestCase
{
    protected UserResource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = Root::instance()->resources->resolve('users');
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

    public function test_a_resource_resolves_query(): void
    {
        $this->resource->with(['documents']);

        $query = $this->resource->resolveQuery($this->app['request']);

        $this->assertInstanceOf(User::class, $query->getModel());
        $this->assertSame(['documents'], array_keys($query->getEagerLoads()));

        $this->assertSame(
            'select * from "users" where "users"."deleted_at" is null',
            $query->toRawSql()
        );
    }

    public function test_a_resource_resolves_filtered_query(): void
    {
        $this->app['request']->merge([
            'users_sort' => ['order' => 'asc', 'by' => 'id'],
            'users_search' => 'test',
        ]);

        $query = $this->resource->resolveFilteredQuery($this->app['request']);

        $this->assertSame(
            'select * from "users" where ("users"."name" like \'%test%\' or "users"."email" like \'%test%\') and "users"."deleted_at" is null order by "users"."id" asc',
            $query->toRawSql()
        );
    }

    public function test_a_resource_resolves_filters(): void
    {
        $filters = $this->resource->resolveFilters($this->app['request']);

        $this->assertTrue($filters->isNotEmpty());
    }

    public function test_a_resource_resolves_actions(): void
    {
        $actions = $this->resource->resolveActions($this->app['request']);

        $this->assertTrue($actions->isNotEmpty());
    }

    public function test_a_resource_resolves_fields(): void
    {
        $fields = $this->resource->resolveFields($this->app['request']);

        $this->assertTrue($fields->isNotEmpty());
    }

    public function test_a_resource_resolves_widgets(): void
    {
        $widgets = $this->resource->resolveWidgets($this->app['request']);

        $this->assertTrue($widgets->isNotEmpty());
    }

    public function test_a_resource_registers_routes(): void
    {
        $action = $this->resource->resolveActions($this->app['request'])->first();

        $this->assertSame('/root/resources/users/actions/send-password-reset-notification', $action->getUri());

        $this->assertArrayHasKey(
            trim($action->getUri(), '/'),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    public function test_a_resource_handles_form_requests(): void
    {
        $user = new User;

        $this->app['request']->merge([
            'name' => 'Test',
            'email' => 'hello@foo.bar',
            'password' => 'password',
        ]);

        $this->resource->handleFormRequest($this->app['request'], $user);

        $this->assertSame(['name' => 'Test', 'email' => 'hello@foo.bar'], $user->only(['name', 'email']));
    }
}
