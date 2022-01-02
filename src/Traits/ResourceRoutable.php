<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Resources\Resource;
use Cone\Root\Root;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

trait ResourceRoutable
{
    use Resolvable {
        Resolvable::resolved as baseResolved;
    }

    /**
     * The URL resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $urlResolver = null;

    /**
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        $this->baseResolved($request, $resource, $key);

        $uri = str_replace(['.', ':'], '/', $key);

        if (! App::routesAreCached()) {
            $this->routes($resource, $uri);
        }

        $uri = sprintf('/%s/%s/%s', Root::getPath(), $resource->getKey(), $uri);

        $this->urlResolver = static function () use ($uri): string {
            return URL::to($uri);
        };

        Event::listen(RouteMatched::class, function (RouteMatched $event) use ($uri): void {
            if ($event->route->uri() === $uri) {
                $event->route->setParameter('resolved', $this);
            }
        });
    }

    /**
     * Regsiter the routes for the async component.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    abstract protected function routes(Resource $resource, string $uri): void;
}
