<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class RelationControllerTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsTo('Author');

        $this->field->async();

        $this->app->make('root')->routes(function ($router) {
            $router->group(
                ['prefix' => $this->resource->getKey().'/fields', 'resource' => $this->resource->getKey()],
                function ($router) {
                    $this->field->registerRoutes($this->request, $router);
                }
            );
        });
    }

    /** @test */
    public function a_relation_controller_has_index()
    {
        $this->request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/fields/author'];
        });

        $model = new Post();

        $results = $this->field
                        ->resolveQuery($this->request, $model)
                        ->paginate()
                        ->setPath('/root/posts/fields/author')
                        ->through(function ($related) use ($model): array {
                            return $this->field->mapOption($this->request, $model, $related);
                        });

        $this->actingAs($this->admin)
            ->get('/root/posts/fields/author')
            ->assertOk()
            ->assertJson($results->toArray());
    }
}
