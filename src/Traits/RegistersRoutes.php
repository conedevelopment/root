<?php

declare(strict_types=1);

namespace Cone\Root\Traits;

use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait RegistersRoutes
{
    /**
     * The URI.
     */
    protected ?string $uri = null;

    /**
     * Get the URI key.
     */
    abstract public function getUriKey(): string;

    /**
     * Get the URI attribute.
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * Get the route prefix.
     */
    public function getRoutePrefix(): string
    {
        return $this->getUriKey();
    }

    /**
     * Get the route parameter name.
     */
    public function getRouteParameterName(): string
    {
        return Str::of(self::class)->classBasename()->lower()->value();
    }

    /**
     * Get the route middleware for the registered routes.
     */
    public function getRouteMiddleware(): array
    {
        return [];
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->uri = Str::start(sprintf('%s/%s', $router->getLastGroupPrefix(), $this->getRoutePrefix()), '/');

        if (! App::routesAreCached()) {
            $router->group([
                'prefix' => $this->getRoutePrefix(),
                'middleware' => $this->getRouteMiddleware(),
            ], function (Router $router): void {
                $this->routes($router);
            });
        }

        $router->matched(function (RouteMatched $event): void {
            if (str_starts_with(Str::start($event->request->path(), '/'), $this->replaceRoutePlaceholders($event->route))) {
                $this->routeMatched($event);
            }
        });
    }

    /**
     * Handle the route matched event.
     */
    public function routeMatched(RouteMatched $event): void
    {
        $event->route->setParameter($this->getRouteParameterName(), $this);
    }

    /**
     * Replace the route placeholders with the route parameters.
     */
    public function replaceRoutePlaceholders(Route $route): string
    {
        $uri = $this->getUri();

        foreach ($route->originalParameters() as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return preg_replace('/\{.*?\}/', 'create', $uri);
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        //
    }
}
