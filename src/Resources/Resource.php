<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Http\Middleware\AuthorizeResource;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Root;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Extracts;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Traits\Authorizable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Resource implements Arrayable
{
    use Authorizable;

    /**
     * The model class.
     *
     * @var string
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected array $with = [];

    /**
     * The fields resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * The filters resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The actions resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The extracts resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $extractsResolver = null;

    /**
     * The widgets resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $widgetsResolver = null;

    /**
     * The resolved components.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Create a new resource instance.
     *
     * @param  string  $model
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for the resource.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return (string) Str::of($this->getModel())->classBasename()->plural()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) Str::of($this->getModel())->classBasename()->headline()->plural();
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    public function getModelName(): string
    {
        return (string) Str::of($this->getModel())->classBasename();
    }

    /**
     * Get the model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelInstance(): Model
    {
        return new ($this->getModel());
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveRouteBinding(Request $request, mixed $value): Model
    {
        $key = strtolower($this->getModelName());

        if (($model = $request->route($key)) instanceof Model) {
            return $model;
        }

        $model = $this->getModelInstance()->resolveRouteBinding($value);

        if (is_null($model)) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), $value);
        }

        $request->route()->setParameter($key, $model);

        return $model;
    }

    /**
     * Set the relations to eagerload.
     *
     * @param  array  $relations
     * @return $this
     */
    public function with(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with);
    }

    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     *
     * @param  array|\Closure  $fields
     * @return $this
     */
    public function withFields(array|Closure $fields): static
    {
        if (is_array($fields)) {
            $fields = static function (Request $request, Fields $collection) use ($fields): Fields {
                return $collection->merge($fields);
            };
        }

        $this->fieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        if (! isset($this->resolved['fields'])) {
            $fields = Fields::make($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $fields = call_user_func_array($this->fieldsResolver, [$request, $fields]);
            }

            $this->resolved['fields'] = $fields;
        }

        return $this->resolved['fields'];
    }

    /**
     * Define the filters for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Set the filters resolver.
     *
     * @param  array|\Closure  $filters
     * @return $this
     */
    public function withFilters(array|Closure $filters): static
    {
        if (is_array($filters)) {
            $filters = static function (Request $request, Filters $collection) use ($filters): Filters {
                return $collection->merge($filters);
            };
        }

        $this->filtersResolver = $filters;

        return $this;
    }

    /**
     * Resolve filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request): Filters
    {
        if (! isset($this->resolved['filters'])) {
            $filters = Filters::make($this->filters($request));

            if (! is_null($this->filtersResolver)) {
                $filters = call_user_func_array($this->filtersResolver, [$request, $filters]);
            }

            $this->resolved['filters'] = $filters;
        }

        return $this->resolved['filters'];
    }

    /**
     * Define the actions for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver.
     *
     * @param  array|\Closure  $actions
     * @return $this
     */
    public function withActions(array|Closure $actions): static
    {
        if (is_array($actions)) {
            $actions = static function (Request $request, Actions $collection) use ($actions): Actions {
                return $collection->merge($actions);
            };
        }

        $this->actionsResolver = $actions;

        return $this;
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
            $actions = Actions::make($this->actions($request));

            if (! is_null($this->actionsResolver)) {
                $actions = call_user_func_array($this->actionsResolver, [$request, $actions]);
            }

            $actions->each->withQuery(function (): Builder {
                return $this->query();
            });

            $this->resolved['actions'] = $actions;
        }

        return $this->resolved['actions'];
    }

    /**
     * Define the extracts for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function extracts(Request $request): array
    {
        return [];
    }

    /**
     * Set the extracts resolver.
     *
     * @param  array|\Closure  $extracts
     * @return $this
     */
    public function withExtracts(array|Closure $extracts): static
    {
        if (is_array($extracts)) {
            $extracts = static function (Request $request, Extracts $collection) use ($extracts): Extracts {
                return $collection->merge($extracts);
            };
        }

        $this->extractsResolver = $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Extracts
     */
    public function resolveExtracts(Request $request): Extracts
    {
        if (! isset($this->resolved['extracts'])) {
            $extracts = Extracts::make($this->extracts($request));

            if (! is_null($this->extractsResolver)) {
                $extracts = call_user_func_array($this->extractsResolver, [$request, $extracts]);
            }

            $extracts->each->withQuery(function (): Builder {
                return $this->query();
            });

            $this->resolved['extracts'] = $extracts;
        }

        return $this->resolved['extracts'];
    }

    /**
     * Define the widgets for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Set the widgets resolver.
     *
     * @param  array|\Closure  $widgets
     * @return $this
     */
    public function withWidgets(array|Closure $widgets): static
    {
        if (is_array($widgets)) {
            $widgets = static function (Request $request, Widgets $collection) use ($widgets): Widgets {
                return $collection->merge($widgets);
            };
        }

        $this->widgetsResolver = $widgets;

        return $this;
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
            $widgets = Widgets::make($this->widgets($request));

            if (! is_null($this->widgetsResolver)) {
                $widgets = call_user_func_array($this->widgetsResolver, [$request, $widgets]);
            }

            $this->resolved['widgets'] = $widgets;
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
            'create' => URL::to(sprintf('%s/%s/create', Root::getPath(), $this->getKey())),
            'index' => URL::to(sprintf('%s/%s', Root::getPath(), $this->getKey())),
        ];
    }

    /**
     * Get the policy.
     *
     * @return mixed
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->getModel());
    }

    /**
     * Map the abilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapAbilities(Request $request): array
    {
        $policy = $this->getPolicy();

        return array_reduce(
            ['viewAny', 'create'],
            function (array $stack, $ability) use ($request, $policy): array {
                return array_merge($stack, [
                    $ability => is_null($policy) || $request->user()->can($ability, $this->getModel()),
                ]);
            },
            []
        );
    }

    /**
     * Map the items.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapItems(Request $request): array
    {
        $query = $this->query();

        $filters = $this->resolveFilters($request)->available($request);

        $items = $filters->apply($request, $query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $model) use ($request): array {
                        return $model->toDisplay($request, $this->resolveFields($request)->available($request, $model));
                    })
                    ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
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
            'model_name' => $this->getModelName(),
            'urls' => App::call([$this, 'mapUrls']),
            'abilities' => App::call([$this, 'mapAbilities']),
        ];
    }

    /**
     * Get the index representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @return array
     */
    public function toIndex(IndexRequest $request): array
    {
        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)
                            ->available($request)
                            ->mapToForm($request, $this->getModelInstance())
                            ->toArray(),
            'extracts' => $this->resolveExtracts($request)->available($request)->toArray(),
            'filters' => $this->resolveFilters($request)->available($request)->toArray(),
            'items' => $this->mapItems($request),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ]);
    }

    /**
     * Get the create representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @return array
     */
    public function toCreate(CreateRequest $request): array
    {
        $model = $this->getModelInstance();

        return array_merge($this->toArray(), [
            'model' => $model->toForm($request, $this->resolveFields($request)->available($request, $model)),
        ]);
    }

    /**
     * Get the show representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toShow(ShowRequest $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->mapToForm($request, $model)->toArray(),
            'model' => $model->toDisplay($request, $this->resolveFields($request)->available($request, $model)),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toEdit(UpdateRequest $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'model' => $model->toForm($request, $this->resolveFields($request)->available($request, $model)),
        ]);
    }

    /**
     * Handle the resource registered event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function registered(Request $request): void
    {
        $this->registerRoutes($request);
    }

    /**
     * Register the routes for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function registerRoutes(Request $request): void
    {
        $this->routeGroup(function (Router $router) use ($request): void {
            if (! App::routesAreCached()) {
                $this->routes($router);
            }

            $this->resolveExtracts($request)->registerRoutes($request, $router);
        });

        $this->routeGroup(function (Router $router) use ($request): void {
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveFields($request)->registerRoutes($request, $router);
            $this->resolveWidgets($request)->registerRoutes($request, $router);
        }, true);
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->get('/', [ResourceController::class, 'index']);
        $router->get('/create', [ResourceController::class, 'create']);
        $router->post('/', [ResourceController::class, 'store']);
        $router->get('/{id}', [ResourceController::class, 'show']);
        $router->get('/{id}/edit', [ResourceController::class, 'edit']);
        $router->patch('/{id}', [ResourceController::class, 'update']);
        $router->delete('/{id}', [ResourceController::class, 'destroy']);
    }

    /**
     * Wrap the given routes into the route group.
     *
     * @param  \Closure  $callback
     * @param  bool  $api
     * @return void
     */
    public function routeGroup(Closure $callback, bool $api = false): void
    {
        Root::routes(function (Router $router) use ($callback): void {
            $router->group([
                'prefix' => $this->getKey(),
                'resource' => $this->getKey(),
                'middleware' => [AuthorizeResource::class],
            ], $callback);
        }, $api);
    }
}
