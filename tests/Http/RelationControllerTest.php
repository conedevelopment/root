<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\HasMany;
use Cone\Root\Models\Medium;
use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class RelationControllerTest extends TestCase
{
    protected HasMany $field;

    protected User $admin;

    protected Medium $medium;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveFields($this->app['request'])
            ->first(function ($field) {
                return $field->getModelAttribute() === 'uploads';
            });

        $this->admin = User::factory()->create();

        $this->medium = $this->admin->uploads()->save(
            Medium::factory()->make(['file_name' => 'test.png'])
        );
    }

    public function test_relation_controller_handles_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/resources/users/'.$this->admin->getKey().'/fields/uploads')
            ->assertOk()
            ->assertViewIs('root::resources.index')
            ->assertViewHas($this->field->toIndex($this->app['request'], $this->admin));
    }

    public function test_relation_controller_handles_create(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/resources/users/'.$this->admin->getKey().'/fields/uploads/create')
            ->assertOk()
            ->assertViewIs('root::resources.form')
            ->assertViewHas($this->field->toCreate($this->app['request'], $this->admin));
    }

    public function test_relation_controller_handles_store(): void
    {
        $this->actingAs($this->admin)
            ->post('/root/resources/users/'.$this->admin->getKey().'/fields/uploads', $data = Medium::factory()->make()->toArray())
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-saved');

        $this->assertTrue(
            $this->admin->refresh()->uploads->contains(fn ($medium) => $medium->file_name === $data['file_name'])
        );
    }

    public function test_relation_controller_handles_show(): void
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/users/{resourceModel}/fields/uploads/{usersUpload}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/resources/users/'.$this->admin->getKey().'/fields/uploads/'.$this->medium->getKey())
            ->assertOk()
            ->assertViewIs('root::resources.show')
            ->assertViewHas($this->field->toShow($this->app['request'], $this->admin, $this->medium));
    }

    public function test_relation_controller_handles_edit(): void
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/users/{resourceModel}/fields/uploads/{usersUpload}/edit'];
        });

        $this->actingAs($this->admin)
            ->get('/root/resources/users/'.$this->admin->getKey().'/fields/uploads/'.$this->medium->getKey().'/edit')
            ->assertOk()
            ->assertViewIs('root::resources.form')
            ->assertViewHas($this->field->toEdit($this->app['request'], $this->admin, $this->medium));
    }

    public function test_relation_controller_handles_update(): void
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('PATCH')['root/users/{resourceModel}/fields/uploads/{usersUpload}'];
        });

        $this->actingAs($this->admin)
            ->patch(
                '/root/resources/users/'.$this->admin->getKey().'/fields/uploads/'.$this->medium->getKey(),
                array_merge($this->medium->toArray(), ['file_name' => 'updated.png'])
            )
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-saved');

        $this->assertSame('updated.png', $this->medium->refresh()->file_name);
    }

    public function test_relation_controller_handles_destroy(): void
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('DELETE')['root/users/{resourceModel}/fields/uploads/{usersUpload}'];
        });

        $this->actingAs($this->admin)
            ->delete('/root/resources/users/'.$this->admin->getKey().'/fields/uploads/'.$this->medium->getKey())
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-deleted');

        $this->assertDatabaseMissing('root_media', $this->medium->only(['id']));
    }
}
