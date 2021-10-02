<?php

namespace Cone\Root\Nodes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Node implements Arrayable
{
    /**
     * The model class.
     *
     * @var string
     */
    protected static string $model;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected static array $with = [];

    /**
     * Get the model for the resource.
     *
     * @return string
     */
    public static function getModel(): string
    {
        return static::$model;
    }

    /**
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(): Builder
    {
        return call_user_func([static::getModel(), 'query'])->with(static::$with);
    }

    /**
     * Get the model instance of the query.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function getModelInstance(): Model
    {
        return static::query()->getModel();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
