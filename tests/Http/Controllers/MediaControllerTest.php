<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\Media;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class MediaControllerTest extends TestCase
{
    protected $field, $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Media('Media');

        $this->medium = Medium::factory()->create();

        $this->resource->routeGroup(function ($router) {
            $router->prefix('fields')->group(function ($router) {
                $this->field->registerRoutes($this->request, $router);
            });
        });
    }

    /** @test */
    public function a_media_controller_has_index()
    {
        $request = ResourceRequest::createFrom($this->request);

        $request->setUserResolver(function () {
            return $this->admin;
        });

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/fields/media'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/fields/media')
            ->assertOk()
            ->assertJson(
                $this->field->mapItems($request, new Post())
            );
    }

    /** @test */
    public function a_media_controller_has_store()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_media_controller_has_destroy()
    {
        $this->assertTrue(true);
    }
}
