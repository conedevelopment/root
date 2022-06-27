<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class Comment extends Model
{
    protected $fillable = ['id', 'contebt'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->shouldReceive('getModel')->andReturn($this);
        $builder->shouldReceive('get')->andReturn($this->results());
        $builder->shouldReceive('latest')->andReturn($builder);
        $builder->shouldReceive('paginate')->andReturn(new LengthAwarePaginator($this->results(), 2, 15, 1));

        return $builder;
    }

    public function post()
    {
        $builder = (new Post())->newQuery();

        $builder->shouldReceive('where')->with('posts.id', '=', 'foreign.value');
        $builder->shouldReceive('whereNotNull');

        return new BelongsTo($builder, $this, 'foreignKey', 'id', 'tag');
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'content' => 'Comment One']),
            new static(['id' => 2, 'content' => 'Comment Two']),
        ]);
    }
}
