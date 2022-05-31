<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\Published;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ActionTest extends TestCase
{
    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishPosts();
    }

    /** @test */
    public function an_action_registers_routes()
    {
        $this->app['router']->prefix('api/posts/actions')->group(function ($router) {
            $this->action->registerRoutes($this->request, $router);
        });

        $this->assertSame('api/posts/actions/publish-posts', $this->action->getUri());

        $this->assertArrayHasKey(
            $this->action->getUri(),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    /** @test */
    public function an_action_has_fields()
    {
        $fields = $this->action->resolveFields($this->request);

        $this->assertTrue($fields->contains(function ($field) {
            return $field->getKey() === 'title';
        }));
    }

    /** @test */
    public function an_action_has_form_representation()
    {
        $model = new Post();

        $fields = $this->action->resolveFields($this->request)->mapToForm($this->request, $model)->toArray();

        $this->assertSame(
            array_merge($this->action->toArray(), [
                'data' => array_column($fields, 'value', 'name'),
                'fields' => $fields,
            ]),
            $this->action->toForm($this->request, $model)
        );
    }

    /** @test */
    public function an_action_can_be_destructive()
    {
        $this->assertFalse($this->action->toArray()['destructive']);

        $this->action->destructive();

        $this->assertTrue($this->action->toArray()['destructive']);

        $this->action->destructive(false);

        $this->assertFalse($this->action->toArray()['destructive']);
    }

    /** @test */
    public function an_action_can_be_confirmable()
    {
        $this->assertFalse($this->action->toArray()['confirmable']);

        $this->action->confirmable();

        $this->assertTrue($this->action->toArray()['confirmable']);

        $this->action->confirmable(false);

        $this->assertFalse($this->action->toArray()['confirmable']);
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->action->resolveQuery($this->request);
    }

    /** @test */
    public function an_action_can_be_berformed()
    {
        $this->action->withQuery(function () {
            return Post::query();
        });

        Event::fake([Published::class]);

        $request = ActionRequest::createFrom($this->request);

        $this->action->perform($request);

        Event::assertDispatched(Published::class);
    }
}
