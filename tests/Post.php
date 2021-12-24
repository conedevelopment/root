<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery;

class Post extends Model
{
    public $foreignKey = 'foreign.value';

    protected $fillable = ['id', 'title'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->shouldReceive('getModel')->andReturn($this);
        $builder->shouldReceive('get')->andReturn($this->results());

        return $builder;
    }

    public function author()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->shouldReceive('where')->with('authors.id', '=', 'foreign.value');
        $builder->shouldReceive('getModel')->andReturn(new Author());

        return new BelongsTo($builder, $this, 'foreignKey', 'id', 'author');
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'title' => 'Post One']),
            new static(['id' => 2, 'title' => 'Post Two']),
        ]);
    }
}
