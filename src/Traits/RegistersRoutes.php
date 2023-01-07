<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Middleware\AuthorizeResolved;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait RegistersRoutes
{
    /**
     * The URI.
     *
     * @var string|null
     */
    protected ?string $uri = null;

    /**
     * Get the key.
     *
     * @return string
     */
    abstract public function getKey(): string;

    /**
     * Set the URI attribute.
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * Register the routes using the given router.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $this->setUri(sprintf('/%s/%s', $router->getLastGroupPrefix(), $this->getKey()));

        if (! App::routesAreCached()) {
            $router->prefix($this->getKey())
                ->middleware([AuthorizeResolved::class])
                ->group(function (Router $router): void {
                    $this->routes($router);
                });
        }

        $router->matched(function (RouteMatched $event): void {
            if (str_starts_with(Str::start($event->route->uri(), '/'), $this->getUri())) {
                $event->route->setParameter('resolved', $this);
            }
        });
    }

    /**
     * The routes that should be registered.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        //
    }
}
