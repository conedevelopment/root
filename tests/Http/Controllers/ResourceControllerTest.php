<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ResourceControllerTest extends TestCase
{
    /** @test */
    public function a_resource_controller_has_index()
    {
        $request = IndexRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Index',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->resource->toIndex($request), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_create()
    {
        $request = CreateRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/create'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/create')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Form',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->resource->toCreate($request), $props));
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
        $request = ShowRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{post}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Show',
                'page.props' => function ($props) use ($request, $model) {
                    return empty(array_diff_key($this->resource->toShow($request, $model), $props));
                },
            ]);
    }

    /** @test */
    public function a_resource_controller_has_edit()
    {
        $request = UpdateRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{post}/edit'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/edit')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Resources/Form',
                'page.props' => function ($props) use ($request, $model) {
                    return empty(array_diff_key($this->resource->toEdit($request, $model), $props));
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
