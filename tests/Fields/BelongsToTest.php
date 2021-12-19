<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mockery as m;

class BelongsToTest extends TestCase
{
    protected $field, $builder;

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
            (new Author())->newQuery()->get()->pluck('id', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );

        $this->field->display('name');

        $this->assertSame(
            (new Author())->newQuery()->get()->pluck('name', 'id')->toArray(),
            $this->field->resolveOptions($this->app['request'], $post)
        );
    }
}

class Author extends Model
{
    protected $fillable = ['id', 'name'];

    public function newQuery()
    {
        $builder = m::mock(Builder::class);

        $builder->shouldReceive('where')->with('authors.id', '=', 'foreign.value');
        $builder->shouldReceive('getModel')->andReturn($this);
        $builder->shouldReceive('get')->andReturn(collect([
            new static(['id' => 1, 'name' => 'Author One']),
            new static(['id' => 2, 'name' => 'Author Two']),
        ]));

        return $builder;
    }
}

class Post extends Model
{
    public $foreignKey = 'foreign.value';

    public function author()
    {
        return $this->belongsTo(Author::class, 'foreignKey');
    }
}
