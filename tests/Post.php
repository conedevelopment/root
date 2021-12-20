<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery as m;

class Post extends Model
{
    public $foreignKey = 'foreign.value';

    protected $fillable = ['id', 'title'];

    public function newQuery()
    {
        $builder = m::mock(Builder::class);

        $builder->shouldReceive('getModel')->andReturn($this);
        $builder->shouldReceive('get')->andReturn(static::results());

        return $builder;
    }

    public function author()
    {
        $builder = m::mock(Builder::class);

        $builder->shouldReceive('where')->with('authors.id', '=', 'foreign.value');
        $builder->shouldReceive('getModel')->andReturn(new Author());
        $builder->shouldReceive('get')->andReturn(Author::results());

        return new BelongsTo($builder, $this, 'foreignKey', 'id', 'author');
    }

    public static function results()
    {
        return collect([
            new static(['id' => 1, 'title' => 'Post One']),
            new static(['id' => 2, 'title' => 'Post Two']),
        ]);
    }
}
