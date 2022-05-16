<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Tests\Post;
use Cone\Root\Tests\PublishPosts;
use Cone\Root\Tests\TestCase;

class ActionTest extends TestCase
{
    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishPosts();

        $this->action->withQuery(function () {
            return Post::query();
        });
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
}
