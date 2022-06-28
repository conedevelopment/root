<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class Author extends Model
{
    protected $fillable = ['id', 'name'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->setModel($this);

        $builder->shouldReceive('get')->andReturn($this->results());
        $builder->shouldReceive('paginate')->andReturn(new LengthAwarePaginator($this->results(), 2, 15, 1));

        return $builder;
    }

    public function posts()
    {
        $builder = (new Post())->newQuery();

        return new HasMany($builder, $this, 'posts.author_id', 'id');
    }

    public function scopeFilter($query)
    {
        return $query;
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'name' => 'Author One']),
            new static(['id' => 2, 'name' => 'Author Two']),
        ]);
    }
}
