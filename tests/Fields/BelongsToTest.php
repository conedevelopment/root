<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class BelongsToTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new BelongsTo('Author');
    }

    /** @test */
    public function a_belongs_to_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_belongs_to_field_has_options()
    {
        $post = new Post();

        $this->assertSame(
            Author::query()->get()->pluck('id', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }

    /** @test */
    public function a_belongs_to_field_has_customizable_options()
    {
        $post = new Post();

        $this->field->display('name');

        $this->assertSame(
            Author::query()->get()->pluck('name', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );

        $closure = function ($request, $model) {
            return strtoupper($model->name);
        };

        $this->field->display($closure);

        $this->assertSame(
            Author::query()->get()->mapWithKeys(function ($model) use ($closure) {
                return [$model->id => $closure($this->app['request'], $model)];
            })->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }

    /** @test */
    public function a_belongs_to_field_has_customizable_query()
    {
        $this->assertTrue(true);
    }
}
