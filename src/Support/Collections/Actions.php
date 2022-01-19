<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\ActionResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Filter the actions that are available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Action $action) use ($request): bool {
                        return $action->authorized($request) && $action->visible($request);
                    })
                    ->values();
    }

    /**
     * Filter the actions that are authorized for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }

    /**
     * Filter the actions that are visible for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function visible(Request $request): static
    {
        return $this->filter->visible($request)->values();
    }

    /**
     * Resolve the action by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Actions\Action
     *
     * @throws \Cone\Root\Exceptions\ActionResolutionException
     */
    public function resolve(string $key): Action
    {
        $action = $this->first(static function (Action $action) use ($key): bool {
            return $action->getKey() === $key;
        });

        if (is_null($action)) {
            throw new ActionResolutionException("Unable to resolve action with key [{$key}].");
        }

        return $action;
    }

    /**
     * Register the action routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('actions')->group(function (Router $router) use ($request): void {
            $this->each->registerRoutes($request, $router);
        });
    }
}
