<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
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
