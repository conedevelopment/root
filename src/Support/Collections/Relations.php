<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Relations\Relation;
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
     * Register the field routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('relations')->group(function (Router $router): void {
            $this->each->registerRoutes($router);
        });
    }
}
