<?php

namespace Cone\Root\Relations;

use Cone\Root\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Relations
{
    /**
     * The relations collection.
     */
    protected Collection $relations;

    /**
     * Create a new relations instance.
     */
    public function __construct(array $relations = [])
    {
        $this->relations = new Collection($relations);
    }

    /**
     * Register the given relations.
     */
    public function register(array|Relation $relations): static
    {
        foreach (Arr::wrap($relations) as $relation) {
            $this->relations->push($relation);
        }

        return $this;
    }

    // hasOne()
    // hasMany()
    // belongsTo()
    // belongsToMany()
    // hasOneThrough()
    // hasManyThrough()
    // morphOne()
    // morphTo()
    // morphMany()
    // morphToMany()

    /**
     * Filter the relations that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
    {
        return $this->relations->filter->authorized($request, $model)->values();
    }

    /**
     * Register the field routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('relations')->group(function (Router $router): void {
            $this->relations->each->registerRoutes($router);
        });
    }
}
