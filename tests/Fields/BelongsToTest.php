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
            Author::query()->get()->map(function ($model) {
                return ['value' => $model->getKey(), 'formatted_value' => $model->getKey()];
            })->toArray(),
            $this->field->resolveOptions($this->request, $post)
        );
    }

    /** @test */
    public function a_belongs_to_field_has_customizable_options()
    {
        $post = new Post();

        $this->field->display('name');

        $this->assertSame(
            Author::query()->get()->map(function ($model) {
                return ['value' => $model->getKey(), 'formatted_value' => $model->name];
            })->toArray(),
            $this->field->resolveOptions($this->request, $post)
        );

        $closure = function ($request, $model) {
            return strtoupper($model->name);
        };

        $this->field->display($closure);

        $this->assertSame(
            Author::query()->get()->map(function ($model) use ($closure) {
                return ['value' => $model->getKey(), 'formatted_value' => $closure($this->request, $model)];
            })->toArray(),
            $this->field->resolveOptions($this->request, $post)
        );
    }

    /** @test */
    public function a_belongs_to_field_has_customizable_query()
    {
        $post = new Post();

        $this->assertSame(
            'select * from "authors"',
            $this->field->resolveQuery($this->request, $post)->getQuery()->toSql()
        );

        $this->field->withQuery(function ($request, $query) {
            return $query->where('authors.name', 'Foo');
        });

        $query = $this->field->resolveQuery($this->request, $post)->getQuery();

        $this->assertSame('select * from "authors" where "authors"."name" = ?', $query->toSql());
        $this->assertSame(['Foo'], $query->getBindings());
    }
}
