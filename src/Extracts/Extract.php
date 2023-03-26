<?php

namespace Cone\Root\Extracts;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Interfaces\HasTable;
use Cone\Root\Tables\Table;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable, HasTable
{
    use Makeable;
    use ResolvesWidgets;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Set the query resolver.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query for the extract.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->resolveWidgets(App::make('request'))->registerRoutes($router);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', ExtractController::class);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'url' => $this->getUri(),
        ];
    }

    /**
     * Get the index representation of the extract.
     */
    public function toIndex(Request $request): array
    {
        return array_merge($this->toArray(), [
            'table' => $this->toTable($request)->build($request),
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->toArray(),
        ]);
    }

    /**
     * Get the table representation of the resource.
     */
    public function toTable(Request $request): Table
    {
        return (new Table($this->resolveQuery($request)->getModel()));
    }
}
