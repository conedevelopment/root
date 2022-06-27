<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\HasMany;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class HasOneOrManyControllerTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Comments');

        $this->field->asSubResource();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('fields')->group(function ($router) {
                $this->field->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function a_has_one_or_many_controller_has_index()
    {
        $request = IndexRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/fields/comments/{rootResource}'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/fields/comments/1')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Relations/Index',
                'page.props' => function ($props) use ($request) {
                    return empty(array_diff_key($this->field->toIndex($request, new Post()), $props));
                },
            ]);
    }
}
