<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Middleware\AuthorizeResolved;
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
     * Get the key.
     *
     * @return string
     */
    abstract public function getKey(): string;

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    abstract public function routes(Router $router): void;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->setUri(
            sprintf('%s/%s', $router->getLastGroupPrefix(), $this->getKey())
        );

        $router->matched(function (RouteMatched $event): void {
            if ($event->route->uri() === $this->getUri()) {
                $event->route->setParameter('resolved', $this);
            }
        });

        if (! App::routesAreCached()) {
            $router->group(
                ['middleware' => [AuthorizeResolved::class]],
                function (Router $router): void {
                    $this->routes($router);
                }
            );
        }
    }
}
