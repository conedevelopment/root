<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class BelongsToManyControllerTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsToMany('Tags');

        $this->field->withFields([
            Text::make('Name'),
        ]);

        $this->field->asSubResource();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('{__post}/fields')->group(function ($router) {
                $this->field->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function a_belongs_to_many_controller_has_store()
    {
        $this->actingAs($this->admin)
            ->post('/root/posts/1/fields/tags', [
                'name' => 'New Tag',
            ])
            ->assertRedirect('/root/posts/1/fields/tags/1')
            ->assertSessionHas('alerts.relation-created');
    }

    /** @test */
    public function a_belongs_to_many_controller_has_show()
    {
        $request = ShowRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $related = $model->tags()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{__post}/fields/tags/{__tag}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/tags/1')
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
    public function a_belongs_to_many_controller_has_edit()
    {
        $request = UpdateRequest::createFrom($this->request);

        $model = Post::query()->get()->first();

        $related = $model->tags()->first();

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/{__post}/fields/tags/{__tag}/edit'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/1/fields/tags/1/edit')
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
    public function a_belongs_to_many_controller_has_update()
    {
        $this->actingAs($this->admin)
            ->patch('/root/posts/1/fields/tags/1', [
                'name' => 'New Comment',
            ])
            ->assertRedirect('/root/posts/1/fields/tags/1/edit')
            ->assertSessionHas('alerts.relation-updated');
    }

    /** @test */
    public function a_belongs_to_many_controller_has_delete()
    {
        $this->actingAs($this->admin)
            ->delete('/root/posts/1/fields/tags/1')
            ->assertRedirect('/root/posts/1/fields/tags')
            ->assertSessionHas('alerts.relation-deleted');
    }
}
