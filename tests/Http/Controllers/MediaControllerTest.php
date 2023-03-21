<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Fields\Media;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

class MediaControllerTest extends TestCase
{
    protected $field;

    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Media('Media');

        $this->medium = Medium::factory()->create();

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
    public function a_media_controller_has_index()
    {
        $request = ResourceRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('GET')['root/posts/fields/media'];
        });

        $this->actingAs($this->admin)
            ->get('/root/posts/fields/media')
            ->assertOk()
            ->assertJson($this->field->mapItems($request, new Post()));
    }

    /** @test */
    public function a_media_controller_has_store()
    {
        Queue::fake();

        $request = ResourceRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('POST')['root/posts/fields/media'];
        });

        $this->actingAs($this->admin)
            ->post('/root/posts/fields/media', [
                'file' => UploadedFile::fake()->image('test.png.chunk'),
            ], [
                'X-Chunk-Index' => 1,
                'X-Chunk-Total' => 2,
            ])
            ->assertNoContent();

        $this->actingAs($this->admin)
            ->post('/root/posts/fields/media', [
                'file' => UploadedFile::fake()->image('test.png.chunk'),
            ], [
                'X-Chunk-Index' => 1,
                'X-Chunk-Total' => 1,
            ])
            ->assertCreated()
            ->assertJson(['name' => 'test']);

        $this->assertDatabaseHas('root_media', ['name' => 'test']);

        Queue::assertPushedWithChain(MoveFile::class, [PerformConversions::class]);
    }

    /** @test */
    public function a_media_controller_has_destroy()
    {
        $request = ResourceRequest::createFrom($this->request);

        $request->setRouteResolver(function () {
            return $this->app['router']->getRoutes()->get('DELETE')['root/posts/fields/media'];
        });

        $this->actingAs($this->admin)
            ->delete('/root/posts/fields/media', ['models' => [$this->medium->getKey()]])
            ->assertNoContent();

        $this->assertDatabaseMissing('root_media', ['id' => $this->medium->getKey()]);
    }
}
