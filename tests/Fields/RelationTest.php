<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Relation;
use Cone\Root\Tests\Author;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

class RelationTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new class('Author') extends Relation
        {
            //
        };
    }

    /** @test */
    public function a_relation_field_has_select_component()
    {
        $this->assertSame('Select', $this->field->getComponent());
    }

    /** @test */
    public function a_relation_field_has_access_to_a_model_relation()
    {
        $model = new Post();

        $this->assertInstanceOf(EloquentRelation::class, $this->field->getRelation($model));

        $field = new class('Author', 'author', function ($related) {
            return $related->belongsTo(Author::class, 'id', 'author_id', 'author');
        }) extends Relation {};

        $this->assertInstanceOf(EloquentRelation::class, $field->getRelation($model));
    }

    /** @test */
    public function a_relation_field_can_be_nullable()
    {
        $this->assertFalse($this->field->isNullable());

        $this->field->nullable();

        $this->assertTrue($this->field->isNullable());
    }

    /** @test */
    public function a_relation_field_has_searchable_columns()
    {
        $this->assertSame(['id'], $this->field->getSearchableColumns());

        $this->field->searchable(true, ['name']);

        $this->assertSame(['name'], $this->field->getSearchableColumns());
    }

    /** @test */
    public function a_relation_field_has_sortable_column()
    {
        $this->assertSame('id', $this->field->getSortableColumn());

        $this->field->sortable(true, 'name');

        $this->assertSame('name', $this->field->getSortableColumn());
    }

    /** @test */
    public function a_relation_field_can_be_async()
    {
        $this->assertFalse($this->field->isAsync());

        $this->field->async();

        $this->assertTrue($this->field->isAsync());

        $this->assertSame('AsyncSelect', $this->field->getComponent());
    }

    /** @test */
    public function a_relation_field_registers_routes()
    {
        $this->field->async();

        $this->app['router']->prefix('posts/fields')->group(function ($router) {
            $this->field->registerRoutes($this->request, $router);
        });

        $this->assertSame('/posts/fields/author', $this->field->getUri());

        $this->assertArrayHasKey(
            trim($this->field->getUri(), '/'),
            $this->app['router']->getRoutes()->get('GET')
        );
    }

    /** @test */
    public function a_relation_field_has_options()
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
    public function a_relation_field_has_customizable_options()
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
    public function a_relation_field_has_customizable_query()
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
