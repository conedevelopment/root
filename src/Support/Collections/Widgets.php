<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Widgets extends Collection
{
    /**
     * Filter the widgets that are visible for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function filterVisible(Request $request): static
    {
        return $this->filter(static function (Widget $widget) use ($request): bool {
                        return $widget->visible($request);
                    })
                    ->values();
    }

    /**
     * Register the widget routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $uri
     * @return void
     */
    public function routes(Request $request, ?string $uri = null): void
    {
        Route::prefix('widgets')->group(function () use ($request, $uri): void {
            $this->each(static function (Widget $widget) use ($request, $uri): void {
                if (! App::routesAreCached()) {
                    $widget->routes($request);
                }

                $widget->setUri("{$uri}/widgets/{$widget->getKey()}");
            });
        });
    }
}
