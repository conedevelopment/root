<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Fields\Text;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class ActionTest extends TestCase
{
    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishPosts();
    }

    /** @test */
    public function an_action_has_key()
    {
        $this->assertSame('publish-posts', $this->action->getKey());
    }

    /** @test */
    public function an_action_has_name()
    {
        $this->assertSame('Publish Posts', $this->action->getName());
    }

    /** @test */
    public function an_action_can_be_destructive()
    {
        $this->assertFalse($this->action->isDestructive());

        $this->action->destructive();

        $this->assertTrue($this->action->isDestructive());

        $this->action->destructive(false);

        $this->assertFalse($this->action->isDestructive());
    }

    /** @test */
    public function an_action_can_be_confirmable()
    {
        $this->assertFalse($this->action->isConfirmable());

        $this->action->confirmable();

        $this->assertTrue($this->action->isConfirmable());

        $this->action->confirmable(false);

        $this->assertFalse($this->action->isConfirmable());
    }

    /** @test */
    public function an_action_throws_exception_if_cant_resolve_query()
    {
        $this->expectException(QueryResolutionException::class);

        $this->action->resolveQuery($this->request);
    }

    /** @test */
    public function an_action_resolves_query()
    {
        $this->action->withQuery(function () {
            return Post::query();
        });

        $this->assertSame(
            Post::query()->toSql(),
            $this->action->resolveQuery($this->request)->toSql()
        );
    }

    /** @test */
    public function an_action_registers_routes()
    {
        $this->app['router']->prefix('posts/actions')->group(function ($router) {
            $this->action->registerRoutes($this->request, $router);
        });

        $this->assertSame('/posts/actions/publish-posts', $this->action->getUri());

        $this->assertArrayHasKey(
            trim($this->action->getUri(), '/'),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    /** @test */
    public function an_action_resolves_fields()
    {
        $this->action->withFields([
            Text::make(__('Name')),
        ]);

        $this->assertSame(
            Fields::make(array_merge($this->action->fields($this->request), [Text::make(__('Name'))]))->toArray(),
            $this->action->resolveFields($this->request)->toArray()
        );
    }

    /** @test */
    public function an_action_has_array_representation()
    {
        $this->assertSame([
            'confirmable' => $this->action->isConfirmable(),
            'destructive' => $this->action->isDestructive(),
            'key' => $this->action->getKey(),
            'name' => $this->action->getName(),
            'url' => $this->action->getUri(),
        ], $this->action->toArray());
    }

    /** @test */
    public function an_action_has_form_representation()
    {
        $model = new Post();

        $fields = $this->action->resolveFields($this->request)
                            ->available($this->request, $model)
                            ->mapToForm($this->request, $model)
                            ->toArray();

        $this->assertSame(array_merge($this->action->toArray(), [
            'data' => array_column($fields, 'value', 'name'),
            'fields' => $fields,
        ]), $this->action->toForm($this->request, $model));
    }

    /** @test */
    public function an_action_has_response_representation()
    {
        $response = $this->createTestResponse($this->action->toResponse($this->request));

        $response->assertRedirect()
                ->assertSessionHas(sprintf('alerts.action-%s', $this->action->getKey()));
    }
}
