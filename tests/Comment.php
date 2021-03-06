<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class Comment extends Model
{
    protected $fillable = ['id', 'content'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->setModel($this);

        $builder->shouldReceive('get')->andReturn($this->results());
        $builder->shouldReceive('paginate')->andReturn(new LengthAwarePaginator($this->results(), 2, 15, 1));

        return $builder;
    }

    public function post()
    {
        $builder = (new Post())->newQuery();

        return new BelongsTo($builder, $this, 'foreignKey', 'id', 'post');
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'content' => 'Comment One']),
            new static(['id' => 2, 'content' => 'Comment Two']),
        ]);
    }

    public function save(array $options = [])
    {
        $this->setAttribute($this->getKeyName(), 1);
    }

    public function delete()
    {
        //
    }
}
