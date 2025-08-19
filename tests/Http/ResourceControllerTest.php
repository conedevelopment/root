<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Root;
use Cone\Root\Tests\Resources\UserResource;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ResourceControllerTest extends TestCase
{
    protected UserResource $resource;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = Root::instance()->resources->resolve('users');

        $this->admin = User::factory()->create(['name' => 'Admin']);
    }

    public function test_resource_controller_handles_index(): void
    {
        $this->actingAs($this->admin)
            ->get($this->resource->getUri())
            ->assertOk()
            ->assertViewIs('root::resources.index')
            ->assertViewHas($this->resource->toIndex($this->app['request']));
    }

    public function test_resource_controller_handles_create(): void
    {
        $this->actingAs($this->admin)
            ->get($this->resource->getUri().'/create')
            ->assertOk()
            ->assertViewIs('root::resources.form')
            ->assertViewHas($this->resource->toCreate($this->app['request']));
    }

    public function test_resource_controller_handles_show(): void
    {
        $this->actingAs($this->admin)
            ->get($this->resource->getUri().'/'.$this->admin->getKey())
            ->assertOk()
            ->assertViewIs('root::resources.show')
            ->assertViewHas($this->resource->toShow($this->app['request'], $this->admin));
    }

    public function test_resource_controller_handles_edit(): void
    {
        $this->actingAs($this->admin)
            ->get($this->resource->getUri().'/'.$this->admin->getKey().'/edit')
            ->assertOk()
            ->assertViewIs('root::resources.form')
            ->assertViewHas($this->resource->toEdit($this->app['request'], $this->admin));
    }

    public function test_resource_controller_handles_store(): void
    {
        $this->actingAs($this->admin)
            ->post($this->resource->getUri(), [
                'email' => 'test@root.local',
                'name' => 'Test User',
                'password' => 'password',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'test@root.local', 'name' => 'Test User']);
    }

    public function test_resource_controller_handles_update(): void
    {
        $this->actingAs($this->admin)
            ->patch($this->resource->getUri().'/'.$this->admin->getKey(), array_merge($this->admin->toArray(), ['name' => 'Test Admin']))
            ->assertRedirect();

        $this->assertSame('Test Admin', $this->admin->refresh()->name);
    }

    public function test_resource_controller_handles_destroy(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete($this->resource->getUri().'/'.$user->getKey())
            ->assertRedirect();

        $this->assertTrue($user->refresh()->trashed());

        $this->actingAs($this->admin)
            ->delete($this->resource->getUri().'/'.$user->getKey())
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->getKey()]);
    }
}
