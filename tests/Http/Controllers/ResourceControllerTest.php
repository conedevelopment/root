<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ResourceControllerTest extends TestCase
{
    /** @test */
    public function a_resource_controller_has_index()
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Index',
                'page.props' => function ($props) {
                    return empty(array_diff_key($this->resource->toIndex($this->app['request']), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_create()
    {
        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/create'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/create')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Form',
                'page.props' => function ($props) {
                    return empty(array_diff_key($this->resource->toCreate($this->app['request']), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_store()
    {
        $this->actingAs($this->admin)
            ->post('/root/posts', [
                'title' => 'Post Two',
            ])
            ->assertRedirect('/root/posts/1')
            ->assertSessionHas('alerts.resource-created');
    }

    /** @test */
    public function a_resource_controller_has_show()
    {
        $model = Post::query()->get()->first();

        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Show',
                'page.props' => function ($props) use ($model) {
                    return empty(array_diff_key($this->resource->toShow($this->app['request'], $model), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_edit()
    {
        $model = Post::query()->get()->first();

        $this->app['request']->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}/edit'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/edit')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Form',
                'page.props' => function ($props) use ($model) {
                    return empty(array_diff_key($this->resource->toEdit($this->app['request'], $model), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_update()
    {
        $this->actingAs($this->admin)
            ->patch('/root/posts/1', [
                'title' => 'Post Two',
            ])
            ->assertRedirect('/root/posts/1/edit')
            ->assertSessionHas('alerts.resource-updated');
    }

    /** @test */
    public function a_resource_controller_has_destroy()
    {
        $this->actingAs($this->admin)
            ->delete('/root/posts/1')
            ->assertRedirect('/root/posts')
            ->assertSessionHas('alerts.resource-deleted');
    }
}
