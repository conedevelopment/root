<?php

namespace Cone\Root\Extracts;

use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Table\Table;
use Cone\Root\Traits\AsTable;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesQuery;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Extract implements Routable
{
    use AsTable;
    use Authorizable;
    use Makeable;
    use ResolvesWidgets;
    use ResolvesQuery;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

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
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $request = App::make('request');

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->table($request)->registerRoutes($router);
            // $this->resolveWidgets($request)->registerRoutes($router);
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
     * Get the table instance for the resource.
     */
    public function toTable(Request $request): Table
    {
        return Table::make()->query(function (Request $request): Builder {
            return $this->resolveQuery($request);
        });
    }

    /**
     * Get the index representation of the extract.
     */
    public function toIndex(Request $request): array
    {
        return [
            'title' => $this->getName(),
            'table' => $this->table($request),
            // 'widgets' => $this->resolveWidgets($request),
        ];
    }
}
