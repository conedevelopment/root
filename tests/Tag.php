<?php

namespace Cone\Root\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class Tag extends Model
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

        $builder->shouldReceive('getModels')->andReturn($builder->get()->all());

        return (new BelongsToMany($builder, $this, 'post_tag', 'post_id', 'tag_id', 'id', 'id', 'posts'))
                    ->using(Pivot::class);
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'name' => 'Tag One']),
            new static(['id' => 2, 'name' => 'Tag Two']),
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
