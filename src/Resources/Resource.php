<?php

declare(strict_types = 1);

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Extracts\Extract;
use Cone\Root\Fields\Field;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Http\Resources\ModelResource;
use Cone\Root\Root;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\MapsAbilities;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesBreadcrumbs;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use JsonSerializable;

class Resource implements Arrayable, Jsonable, JsonSerializable
{
    use Authorizable;
    use MapsAbilities;
    use ResolvesActions;
    use ResolvesBreadcrumbs;
    use ResolvesExtracts;
    use ResolvesFields;
    use ResolvesFilters;
    use ResolvesWidgets;

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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected array $withCount = [];

    /**
     * The icon for the resource.
     *
     * @var string
     */
    protected string $icon = 'inventory-2';

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
        return Str::of($this->getModel())->classBasename()->plural()->kebab()->toString();
    }

    /**
     * Get the route key name.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('__')->toString();
    }

    /**
     * Get the URI of the resource.
     *
     * @return string
     */
    public function getUri(): string
    {
        return Str::start(sprintf('%s/%s', Root::getPath(), $this->getKey()), '/');
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->headline()->plural()->toString());
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    public function getModelName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->toString());
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
     * Set the resouce icon.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the resource icon.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
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
     * Set the relation counts to eagerload.
     *
     * @param  array  $relations
     * @return $this
     */
    public function withCount(array $relations): static
    {
        $this->withCount = $relations;

        return $this;
    }

    /**
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with)->withCount($this->withCount);
    }

    /**
     * Resolve the query for the given request.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function resolveQuery(ResourceRequest $request): Builder
    {
        return $this->query();
    }

    /**
     * Resolve the resource model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
    {
        return $this->resolveQuery($request)->findOrFail($id);
    }

    /**
     * Define the filters for the resource.
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
        })->withQuery(function (RootRequest $request): Builder {
            return $this->resolveFilters($request)
                        ->available($request)
                        ->apply($request, $this->query());
        });
    }

    /**
     * Handle the resolving event on the extract instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Extracts\Extract  $extract
     * @return void
     */
    protected function resolveExtract(RootRequest $request, Extract $extract): void
    {
        $extract->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->withQuery(function (): Builder {
            return $this->query();
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
     * Map the URLs.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function mapUrls(RootRequest $request): array
    {
        return [
            'index' => $this->getUri(),
            'create' => sprintf('%s/create', $this->getUri()),
        ];
    }

    /**
     * Map the items.
     *
     * @param \Cone\Root\Http\Requests\IndexRequest  $request
     * @return array
     */
    public function mapItems(IndexRequest $request): array
    {
        $filters = $this->resolveFilters($request)->available($request);

        $query = $this->resolveQuery($request);

        $items = $filters->apply($request, $query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->setPath($this->getUri())
                    ->through(function (Model $model) use ($request): array {
                        return $this->mapItem($request, $model)->toDisplay(
                            $request, $this->resolveFields($request)->available($request, $model)
                        );
                    })
                    ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
    }

/**
     * Map the related model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Cone\Root\Http\Resources\ModelResource
     */
    public function mapItem(ResourceRequest $request, Model $model): ModelResource
    {
        return new ModelResource($model);
    }

    /**
     * Get the mappable abilities.
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return ['viewAny', 'create'];
    }

    /**
     * Handle the created event.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function created(CreateRequest $request, Model $model): void
    {
        //
    }

    /**
     * Handle the updated event.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function updated(UpdateRequest $request, Model $model): void
    {
        //
    }

    /**
     * Handle the deleted event.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleted(ResourceRequest $request, Model $model): void
    {
        //
    }

    /**
     * Resolve the breadcrumbs for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function resolveBreadcrumbs(RootRequest $request): array
    {
        $breadcrumbs = [
            Root::getPath() => __('Dashboard'),
            $this->getUri() => $this->getName(),
        ];

        $model = $request->route($this->getRouteKeyName());

        if ($request instanceof CreateRequest) {
            $breadcrumbs[sprintf('%s/create', $this->getUri())] = __('Create');
        }

        if ($request instanceof ShowRequest || $request instanceof UpdateRequest) {
            $breadcrumbs[sprintf('%s/%s', $this->getUri(), $model->getKey())] = $model->getKey();
        }

        if ($request instanceof UpdateRequest) {
            $breadcrumbs[sprintf('%s/%s/edit', $this->getUri(), $model->getKey())] = __('Edit');
        }

        return $breadcrumbs;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $request = App::make(RootRequest::class);

        return [
            'abilities' => $this->mapAbilities($request, $this->getModelInstance()),
            'key' => $this->getKey(),
            'icon' => $this->getIcon(),
            'model_name' => $this->getModelName(),
            'name' => $this->getName(),
            'urls' => $this->mapUrls($request),
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Serialize the object as JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the index representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @return array
     */
    public function toIndex(IndexRequest $request): array
    {
        return [
            'actions' => $this->resolveActions($request)
                            ->available($request)
                            ->mapToForm($request, $this->getModelInstance())
                            ->toArray(),
            'breadcrumbs' => $this->resolveBreadcrumbs($request),
            'extracts' => $this->resolveExtracts($request)->available($request)->toArray(),
            'filters' => $this->resolveFilters($request)->available($request)->mapToForm($request)->toArray(),
            'items' => $this->mapItems($request),
            'resource' => $this->toArray(),
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ];
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

        return [
            'breadcrumbs' => $this->resolveBreadcrumbs($request),
            'model' => (new ModelResource($model))->toForm(
                $request, $this->resolveFields($request)->available($request, $model)
            ),
            'resource' => $this->toArray(),
            'title' => __('Create :model', ['model' => $this->getModelName()]),
        ];
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
        return [
            'actions' => $this->resolveActions($request)->available($request)->mapToForm($request, $model)->toArray(),
            'breadcrumbs' => $this->resolveBreadcrumbs($request),
            'model' => (new ModelResource($model))->toDisplay(
                $request, $this->resolveFields($request)->available($request, $model)
            ),
            'resource' => $this->toArray(),
            'title' => __(':model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ];
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
        return [
            'breadcrumbs' => $this->resolveBreadcrumbs($request),
            'model' => (new ModelResource($model))->toForm(
                $request, $this->resolveFields($request)->available($request, $model)
            ),
            'resource' => $this->toArray(),
            'title' => __('Edit :model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
        ];
    }

    /**
     * Handle the resource registered event.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return void
     */
    public function registered(RootRequest $request): void
    {
        $this->registerRoutes($request);

        App::make('router')->bind($this->getRouteKeyName(), function (string $id): Model {
            return $id === 'create'
                ? $this->getModelInstance()
                : $this->resolveRouteBinding(App::make(ResourceRequest::class), $id);
        });

        App::make('router')->pattern(
            $this->getRouteKeyName(),
            '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|create'
        );
    }

    /**
     * Register the routes for the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return void
     */
    protected function registerRoutes(RootRequest $request): void
    {
        $this->routeGroup(function (Router $router) use ($request): void {
            if (! App::routesAreCached()) {
                $router->as(sprintf('%s.', $this->getKey()))->group(function (Router $router): void {
                    $this->routes($router);
                });
            }

            $this->resolveExtracts($request)->registerRoutes($request, $router);
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveWidgets($request)->registerRoutes($request, $router);

            $router->prefix("{{$this->getRouteKeyName()}}")->group(function ($router) use ($request) {
                $this->resolveFields($request)->registerRoutes($request, $router);
            });
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
        $router->get('/', [ResourceController::class, 'index'])->name('index');
        $router->get('/create', [ResourceController::class, 'create'])->name('create');
        $router->post('/', [ResourceController::class, 'store'])->name('store');
        $router->get("{{$this->getRouteKeyName()}}", [ResourceController::class, 'show'])->name('show');
        $router->get("{{$this->getRouteKeyName()}}/edit", [ResourceController::class, 'edit'])->name('edit');
        $router->patch("{{$this->getRouteKeyName()}}", [ResourceController::class, 'update'])->name('update');
        $router->delete("{{$this->getRouteKeyName()}}", [ResourceController::class, 'destroy'])->name('destroy');
    }

    /**
     * Wrap the given routes into the route group.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function routeGroup(Closure $callback): void
    {
        Root::routes(function (Router $router) use ($callback): void {
            $router->group(['prefix' => $this->getKey(), 'resource' => $this->getKey()], $callback);
        });
    }
}
