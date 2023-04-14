<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Relations extends Collection
{
    /**
     * Register the given relations.
     */
    public function register(array|Relation $relations): static
    {
        foreach (Arr::wrap($relations) as $relation) {
            $this->push($relation);
        }

        return $this;
    }

    /**
     * Filter the relations that are available for the current request and model.
     */
    public function authorized(Request $request, ?Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Map the relations to table.
     */
    public function mapToTable(Request $request, Model $model): array
    {
        return $this->map->toTable($request, $model)->toArray();
    }

    /**
     * Register the field routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('relations')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
