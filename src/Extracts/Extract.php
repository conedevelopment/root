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
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Resources\ModelResource;
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
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable
{
    use Authorizable;
    use Makeable;
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;
    use ResolvesWidgets;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The query resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $queryResolver = null;

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->toString();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->toString());
    }

    /**
     * Set the query resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query for the extract.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cone\Root\Exceptions\QueryResolutionException
     */
    public function resolveQuery(RootRequest $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Define the filters for the extract.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function filters(RootRequest $request): array
    {
        $fields = $this->resolveFields($request)->available($request);

        $searchables = $fields->searchable($request);

        $sortables = $fields->sortable($request);

        return array_values(array_filter([
            $searchables->isNotEmpty() ? Search::make($searchables) : null,
            $sortables->isNotEmpty() ? Sort::make($sortables) : null,
        ]));
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Handle the resolving event on the filter instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Filters\Filter  $filter
     * @return void
     */
    protected function resolveFilter(RootRequest $request, Filter $filter): void
    {
        $filter->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Handle the resolving event on the action instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Actions\Action  $action
     * @return void
     */
    protected function resolveAction(RootRequest $request, Action $action): void
    {
        $action->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->withQuery(function () use ($request): Builder {
            return $this->resolveQuery($request);
        });
    }

    /**
     * Handle the resolving event on the widget instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Widgets\Widget  $widget
     * @return void
     */
    protected function resolveWidget(RootRequest $request, Widget $widget): void
    {
        $widget->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Map the items.
     *
     * @param  \Cone\Root\Http\Requests\ExtractRequest  $request
     * @return array
     */
    public function mapItems(ExtractRequest $request): array
    {
        $query = $this->resolveQuery($request);

        $filters = $this->resolveFilters($request)->available($request);

        $items = $filters->apply($request, $query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $model) use ($request): array {
                        return (new ModelResource($model))->toDisplay(
                            $request, $this->resolveFields($request)->available($request, $model)
                        );
                    })
                    ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
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
        $this->defaultRegisterRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveWidgets($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->get('/', ExtractController::class);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
           'key' => $this->getKey(),
           'name' => $this->getName(),
           'url' => URL::to($this->getUri()),
        ];
    }

    /**
     * Get the index representation of the extract.
     *
     * @param  \Cone\Root\Http\Requests\ExtractRequest  $request
     * @return array
     */
    public function toIndex(ExtractRequest $request): array
    {
        return [
            'actions' => $this->resolveActions($request)->available($request)->toArray(),
            'extract' => $this->toArray(),
            'filters' => $this->resolveFilters($request)->available($request)->mapToForm($request)->toArray(),
            'items' => $this->mapItems($request),
            'resource' => $request->resource()->toArray(),
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ];
    }
}
