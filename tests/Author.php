<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;

class Author extends Model
{
    protected $fillable = ['id', 'name'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->shouldReceive('getModel')->andReturn($this);
        $builder->shouldReceive('get')->andReturn($this->results());

        return $builder;
    }

    public function posts()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->shouldReceive('where')->with('posts.author_id', '=', null);
        $builder->shouldReceive('whereNotNull');
        $builder->shouldReceive('getModel')->andReturn(new Post());

        return new HasMany($builder, $this, 'posts.author_id', 'id');
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'name' => 'Author One']),
            new static(['id' => 2, 'name' => 'Author Two']),
        ]);
    }
}
