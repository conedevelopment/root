<?php

namespace Cone\Root\Tests;

use Cone\Root\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class Post extends Model
{
    use HasMedia;

    public $foreignKey = 'foreign.value';

    protected $fillable = ['id', 'title'];

    public function newQuery()
    {
        $builder = Mockery::mock(Builder::class)->makePartial();

        $builder->setQuery(parent::newQuery()->getQuery());

        $builder->setModel($this);

        $builder->shouldReceive('get')->andReturn($this->results());
        $builder->shouldReceive('findMany')->andReturn($this->results());
        $builder->shouldReceive('paginate')->andReturn(new LengthAwarePaginator($this->results(), 2, 15, 1));

        return $builder;
    }

    public function author()
    {
        $builder = (new Author())->newQuery();

        $builder->shouldReceive('where')->with('authors.id', '=', 'foreign.value');

        return new BelongsTo($builder, $this, 'foreignKey', 'id', 'author');
    }

    public function comments()
    {
        $builder = (new Comment())->newQuery();

        $builder->shouldReceive('where')->with('comments.post_id', '=', null);
        $builder->shouldReceive('whereNotNull');

        return new HasMany($builder, $this, 'comments.post_id', 'id');
    }

    protected function results()
    {
        return collect([
            new static(['id' => 1, 'title' => 'Post One']),
            new static(['id' => 2, 'title' => 'Post Two']),
        ]);
    }

    public function save(array $options = [])
    {
        $this->setAttribute($this->getKeyName(), 2);
    }

    public function delete()
    {
        //
    }
}
