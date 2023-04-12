<?php

namespace Cone\Root\Extracts;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Fields\Field;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable, Routable
{
    use Authorizable;
    use Makeable;
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;
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
     * Define the filters for the extract.
     */
    public function filters(Request $request): array
    {
        $fields = $this->resolveFields($request)->authorized($request);

        $searchables = $fields->searchable($request);

        $sortables = $fields->sortable($request);

        return array_values(array_filter([
            $searchables->isNotEmpty() ? Search::make($searchables) : null,
            $sortables->isNotEmpty() ? Sort::make($sortables) : null,
        ]));
    }

    /**
     * Handle the resolving event on the field instance.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Handle the resolving event on the filter instance.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        $filter->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->withQuery(function (Request $request): Builder {
            return $this->resolveFilters($request)
                        ->authorized($request)
                        ->apply($request, $this->resolveQuery($request));
        });
    }

    /**
     * Handle the resolving event on the widget instance.
     */
    protected function resolveWidget(Request $request, Widget $widget): void
    {
        $widget->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Map the items.
     */
    public function mapItems(Request $request): array
    {
        $query = $this->resolveQuery($request);

        $filters = $this->resolveFilters($request)->authorized($request);

        $items = $filters->apply($request, $query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->setPath($this->getUri())
                    ->through(function (Model $model) use ($request): array {
                        return $request->route('rootResource')
                                        ->newItem($model)
                                        ->toDisplay(
                                            $request,
                                            $this->resolveFields($request)->authorized($request, $model)
                                        );
                    })
                    ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $request = App::make('request');

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($router);
            $this->resolveActions($request)->registerRoutes($router);
            $this->resolveWidgets($request)->registerRoutes($router);
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
        return [
            'actions' => $this->resolveActions($request)
                            ->authorized($request)
                            ->mapToForm($request, $this->resolveQuery($request)->getModel())
                            ->toArray(),
            'filters' => $this->resolveFilters($request)
                            ->authorized($request)
                            ->mapToForm($request)
                            ->toArray(),
            'items' => $this->mapItems($request),
            'title' => $this->getName(),
            'breadcrumbs' => [],
            'widgets' => $this->resolveWidgets($request)->authorized($request)->toArray(),
        ];
    }
}
