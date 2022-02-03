<?php

namespace Cone\Root\Extracts;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable
{
    use Authorizable;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The resolved components.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * The query resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $queryResolver = null;

    /**
     * Make a new extract instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return (string) Str::of(static::class)->classBasename()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) Str::of(static::class)->classBasename()->headline();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cone\Root\Exceptions\QueryResolutionException
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Define the fields for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        if (! isset($this->resolved['fields'])) {
            $this->resolved['fields'] = Fields::make($this->fields($request));

            $this->resolved['fields']->each->mergeAuthorizationResolver(function (Request $request): bool {
                return $this->authorized($request);
            });
        }

        return $this->resolved['fields'];
    }

    /**
     * Define the filters for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request): Filters
    {
        if (! isset($this->resolved['filters'])) {
            $this->resolved['filters'] = Filters::make($this->filters($request));

            $this->resolved['filters']->each->mergeAuthorizationResolver(function (Request $request): bool {
                return $this->authorized($request);
            });
        }

        return $this->resolved['filters'];
    }

    /**
     * Define the actions for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Actions
     */
    public function resolveActions(Request $request): Actions
    {
        if (! isset($this->resolved['actions'])) {
            $this->resolved['actions'] = Actions::make($this->actions($request));

            $this->resolved['actions']->each(function (Action $action) use ($request): void {
                $action->mergeAuthorizationResolver(function (Request $request): bool {
                    return $this->authorized($request);
                });

                $action->withQuery(function () use ($request): Builder {
                    return $this->resolveQuery($request);
                });
            });
        }

        return $this->resolved['actions'];
    }

    /**
     * Define the widgets for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the widgets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(Request $request): Widgets
    {
        if (! isset($this->resolved['widgets'])) {
            $this->resolved['widgets'] = Widgets::make($this->widgets($request));

            $this->resolved['widgets']->each->mergeAuthorizationResolver(function (Request $request): bool {
                return $this->authorized($request);
            });
        }

        return $this->resolved['widgets'];
    }

    /**
     * Map the URLs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapUrls(Request $request): array
    {
        return [
            'create' => str_replace("/extracts/{$this->getKey()}", '', URL::to($this->getUri())),
            'index' => str_replace("/extracts/{$this->getKey()}", '', URL::to($this->getUri())),
        ];
    }

    /**
     * Register the extract routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->defaultRegisterRoutes($request, $router);

        $router->prependGroupStackPrefix('api')
                ->prefix($this->getKey())
                ->group(function (Router $router) use ($request): void {
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
        $router->get($this->getKey(), ExtractController::class);
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
        $filters = $this->resolveFilters($request)->available($request);

        $query = $this->resolveQuery($request)
                    ->tap(static function (Builder $query) use ($request, $filters): void {
                        $filters->apply($request, $query)->latest();
                    })
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $model) use ($request): array {
                        return $model->toDisplay($request, $this->resolveFields($request)->available($request, $model));
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'urls' => $this->mapUrls($request),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ]);
    }
}
