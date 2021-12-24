<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasMany;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;

class HasManyTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('Posts');
    }

    /** @test */
    public function a_has_many_field_has_defaults()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_has_many_field_has_options()
    {
        $post = new Author();

        $this->assertSame(
            Post::query()->get()->pluck('id', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );

        $this->field->display('title');

        $this->assertSame(
            Post::query()->get()->pluck('title', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }

    /** @test */
    public function a_has_many_field_has_customizable_options()
    {
        $author = new Author();

        $this->field->display('title');

        $this->assertSame(
            Post::query()->get()->pluck('title', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $author)
        );

        $closure = function ($request, $model) {
            return strtoupper($model->title);
        };

        $this->field->display($closure);

        $this->assertSame(
            Post::query()->get()->mapWithKeys(function ($model) use ($closure) {
                return [$model->id => $closure($this->app['request'], $model)];
            })->toArray(),
            $this->field->resolveOptions($this->app['request'], $author)
        );
    }

    /** @test */
    public function a_has_many_field_has_customizable_query()
    {
        $author = new Author();

        $this->assertSame(
            'select * from "posts"',
            $this->field->resolveQuery($this->app['request'], $author)->getQuery()->toSql()
        );

        $this->field->withQuery(function ($request, $query) {
            return $query->where('posts.title', 'Foo');
        });

        $query = $this->field->resolveQuery($this->app['request'], $author)->getQuery();

        $this->assertSame('select * from "posts" where "posts"."title" = ?', $query->toSql());
        $this->assertSame(['Foo'], $query->getBindings());
    }
}
