<?php

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
use Cone\Root\Root;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use JsonSerializable;

class Resource implements Arrayable, Jsonable, JsonSerializable
{
    use Authorizable;
    use ResolvesActions;
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
     * Get the route key.
     *
     * @return string
     */
    public function getRouteKey(): string
    {
        return Str::of($this->getKey())->singular()->toString();
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
     * Retrieve the model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveRouteBinding(RootRequest $request, mixed $value): Model
    {
        $key = $this->getRouteKey();

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
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with);
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

        return [
            Search::make($fields->searchable($request)),
            Sort::make($fields->sortable($request)),
        ];
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
        })->withQuery(function (): Builder {
            return $this->query();
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
        $actions = array_fill_keys(['create', 'index'], null);

        foreach ($actions as $action => $value) {
            $actions[$action] = URL::route(sprintf('root.%s.%s', $this->getKey(), $action));
        }

        return $actions;
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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function mapAbilities(RootRequest $request): array
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
     * @param \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function mapItems(RootRequest $request): array
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
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'abilities' => App::call([$this, 'mapAbilities']),
            'key' => $this->getKey(),
            'icon' => $this->getIcon(),
            'model_name' => $this->getModelName(),
            'name' => $this->getName(),
            'urls' => App::call([$this, 'mapUrls']),
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
        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)
                            ->available($request)
                            ->mapToForm($request, $this->getModelInstance())
                            ->toArray(),
            'extracts' => $this->resolveExtracts($request)->available($request)->toArray(),
            'filters' => $this->resolveFilters($request)->available($request)->mapToForm($request)->toArray(),
            'items' => $this->mapItems($request),
            'title' => $this->getName(),
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
            'title' => __('Create :model', ['model' => $this->getModelName()]),
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
            'title' => __(':model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
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
            'title' => __('Edit :model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
        ]);
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
                $router->as("{$this->getKey()}.")->group(function (Router $router): void {
                    $this->routes($router);
                });
            }

            $this->resolveExtracts($request)->registerRoutes($request, $router);
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveFields($request)->registerRoutes($request, $router);
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
        $router->get('/', [ResourceController::class, 'index'])->name('index');
        $router->get('/create', [ResourceController::class, 'create'])->name('create');
        $router->post('/', [ResourceController::class, 'store'])->name('store');
        $router->get('/{id}', [ResourceController::class, 'show'])->name('show');
        $router->get('/{id}/edit', [ResourceController::class, 'edit'])->name('edit');
        $router->patch('/{id}', [ResourceController::class, 'update'])->name('update');
        $router->delete('/{id}', [ResourceController::class, 'destroy'])->name('destroy');
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
