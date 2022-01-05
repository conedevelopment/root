<?php

namespace Cone\Root\Traits;

use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

trait RegistersRoutes
{
    /**
     * The URI.
     *
     * @var string|null
     */
    protected ?string $uri = null;

    /**
     * Set the URI attribute.
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = sprintf('%s/%s', $uri, $this->getKey());
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->setUri($router->getLastGroupPrefix());

        $router->matched(function (RouteMatched $event): void {
            if ($event->route->uri() === $this->getUri()) {
                $event->route->setParameter('resolved', $this);
            }
        });

        if (! App::routesAreCached()) {
            $this->routes($router);
        }
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    abstract public function routes(Router $router): void;
}
