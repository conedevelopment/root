<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class HasOneOrManyControllerTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Comments');

        $this->field->withFields([
            Text::make('Content'),
        ]);

        $this->field->asSubResource();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('{resource_post}/fields')->group(function ($router) {
                $this->field->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function a_has_one_or_many_controller_has_index()
    {
        $request = IndexRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}/fields/comments'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/comments')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Relations/Index',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->field->toIndex($request, new Post()), $props));
                },
            ]);
    }

    /** @test */
    public function a_has_one_or_many_controller_has_create()
    {
        $request = CreateRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}/fields/comments/create'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/comments/create')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Relations/Form',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->field->toCreate($request, new Post()), $props));
                },
            ]);
    }

    /** @test */
    public function a_has_one_or_many_controller_has_store()
    {
        $this->actingAs($this->admin)
            ->post('/root/posts/1/fields/comments', [
                'content' => 'New Comment',
            ])
            ->assertRedirect('/root/posts/1/fields/comments/1')
            ->assertSessionHas('alerts.relation-created');
    }

    /** @test */
    public function a_has_one_or_many_controller_has_show()
    {
        $request = ShowRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $related = $model->comments()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}/fields/comments/{relation_comment}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/comments/1')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Relations/Show',
                'page.props' => function ($props) use ($request, $model, $related) {
                    return empty(array_diff_key($this->field->toShow($request, $model, $related), $props));
                },
            ]);
    }

    /** @test */
    public function a_has_one_or_many_controller_has_edit()
    {
        $request = UpdateRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $related = $model->comments()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{resource_post}/fields/comments/{relation_comment}/edit'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/comments/1/edit')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Relations/Form',
                'page.props' => function ($props) use ($request, $model, $related) {
                    return empty(array_diff_key($this->field->toEdit($request, $model, $related), $props));
                },
            ]);
    }

    /** @test */
    public function a_has_one_or_many_controller_has_update()
    {
        $this->actingAs($this->admin)
            ->patch('/root/posts/1/fields/comments/1', [
                'content' => 'New Comment',
            ])
            ->assertRedirect('/root/posts/1/fields/comments/1/edit')
            ->assertSessionHas('alerts.relation-updated');
    }

    /** @test */
    public function a_has_one_or_many_controller_has_delete()
    {
        $this->actingAs($this->admin)
            ->delete('/root/posts/1/fields/comments/1')
            ->assertRedirect('/root/posts/1/fields/comments')
            ->assertSessionHas('alerts.relation-deleted');
    }
}
