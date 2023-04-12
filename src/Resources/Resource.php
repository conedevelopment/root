<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\Action;
use Cone\Root\Enums\ResourceContext;
use Cone\Root\Extracts\Extract;
use Cone\Root\Fields\Field;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Forms\Form;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Root;
use Cone\Root\Tables\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesRelations;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Resource implements Arrayable, Routable
{
    use Authorizable;
    use ResolvesActions;
    use ResolvesExtracts;
    use ResolvesFields;
    use ResolvesFilters;
    use ResolvesRelations;
    use ResolvesWidgets;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The model class.
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [];

    /**
     * The relations to eager load on every query.
     */
    protected array $withCount = [];

    /**
     * The icon for the resource.
     */
    protected string $icon = 'inventory-2';

    /**
     * Create a new resource instance.
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->plural()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the route key name.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('resource_')->value();
    }

    /**
     * Get the URI of the resource.
     */
    public function getUri(): string
    {
        return Str::start(sprintf('%s/%s', App::make(Root::class)->getPath(), $this->getKey()), '/');
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->headline()->plural()->value());
    }

    /**
     * Get the model name.
     */
    public function getModelName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->value());
    }

    /**
     * Get the model instance.
     */
    public function getModelInstance(): Model
    {
        return new ($this->getModel());
    }

    /**
     * Set the resource icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the resource icon.
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the policy for the model.
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->getModel());
    }

    /**
     * Set the relations to eagerload.
     */
    public function with(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Set the relation counts to eagerload.
     */
    public function withCount(array $relations): static
    {
        $this->withCount = $relations;

        return $this;
    }

    /**
     * Make a new Eloquent query instance.
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with)->withCount($this->withCount);
    }

    /**
     * Resolve the query for the given request.
     */
    public function resolveQuery(Request $request): Builder
    {
        return $this->query();
    }

    /**
     * Resolve the resource model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\Request  $request
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->resolveQuery($request)
                    ->when($this->isSoftDeletable(), static function (Builder $query): Builder {
                        return $query->withTrashed();
                    })
                    ->findOrFail($id);
    }

    /**
     * Define the filters for the resource.
     */
    public function filters(Request $request): array
    {
        $fields = $this->resolveFields($request)
                    ->visible(ResourceContext::Index->value)
                    ->authorized($request, $this->getModelInstance());

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
     * Handle the resolving event on the extract instance.
     */
    protected function resolveExtract(Request $request, Extract $extract): void
    {
        $extract->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->withQuery(function (Request $request): Builder {
            return $this->resolveQuery($request);
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
     * Make a new resourcable instance.
     */
    public function newResourcable(Model $model): Resourcable
    {
        return (new Resourcable($model))->url(function () use ($model): string {
            return sprintf('%s/%s', $this->getUri(), $model->getRouteKey());
        });
    }

    /**
     * Handle the created event.
     */
    public function created(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the updated event.
     */
    public function updated(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the deleted event.
     */
    public function deleted(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the restored event.
     */
    public function restored(Request $request, Model $model): void
    {
        //
    }

    /**
     * Determine if the model soft deletable.
     */
    public function isSoftDeletable(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->getModel()));
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        $policy = $this->getPolicy();

        return [
            'key' => $this->getKey(),
            'icon' => $this->getIcon(),
            'model_name' => $this->getModelName(),
            'name' => $this->getName(),
            'urls' => [
                'index' => $this->getUri(),
                'create' => sprintf('%s/create', $this->getUri()),
            ],
            'abilities' => [
                'viewAny' => is_null($policy) || Gate::allows('viewAny', $this->getModel()),
                'create' => is_null($policy) || Gate::allows('create', $this->getModel()),
            ],
        ];
    }

    /**
     * Get the table representation of the resource.
     */
    public function toTable(Request $request): Table
    {
        return new Table(
            $this->resolveQuery($request),
            $this->resolveFields($request)->visible(ResourceContext::Index->value)->authorized($request, $this->getModelInstance()),
            $this->resolveActions($request)->visible(ResourceContext::Index->value)->authorized($request, $this->getModelInstance()),
            $this->resolveFilters($request)->authorized($request)
        );
    }

    /**
     * Get the form representation of the resource.
     */
    public function toForm(Request $request, Model $model): Form
    {
        $fields = $this->resolveFields($request)
                    ->visible($model->exists ? ResourceContext::Update->value : ResourceContext::Create->value)
                    ->authorized($request, $model);

        return (new Form($model, $fields))->url(function () use ($model): string {
            return $model->exists ? sprintf('%s/%s', $this->getUri(), $model->getRouteKey()) : $this->getUri();
        });
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return [
            'breadcrumbs' => [],
            'table' => $this->toTable($request)->toData($request),
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ];
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        $model = $this->getModelInstance();

        return [
            'breadcrumbs' => [],
            'model' => $this->toForm($request, $model)->toSchema($request),
            'resource' => $this->toArray(),
            'title' => __('Create :model', ['model' => $this->getModelName()]),
        ];
    }

    /**
     * Get the show representation of the resource.
     */
    public function toShow(Request $request, Model $model): array
    {
        return [
            'actions' => $this->resolveActions($request)
                            ->visible(ResourceContext::Show->value)
                            ->authorized($request, $model)
                            ->mapToForm($request, $model)
                            ->toArray(),
            'breadcrumbs' => [],
            'model' => $this->newResourcable($model)->toDisplay(
                $request, $this->resolveFields($request)->visible(ResourceContext::Show->value)->authorized($request, $model)
            ),
            'title' => __(':model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
            // relations
        ];
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return [
            'breadcrumbs' => [],
            'model' => $this->toForm($request, $model)->toSchema($request),
            'resource' => $this->toArray(),
            'title' => __('Edit :model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
        ];
    }

    /**
     * Get the navigation compatible format of the resource.
     */
    public function toNavigation(Request $request): array
    {
        return array_merge($this->toArray(), [
            'links' => $this->resolveExtracts($request)
                            ->authorized($request)
                            ->map(static function (Extract $extract): array {
                                return [
                                    'url' => $extract->getUri(),
                                    'label' => $extract->getName(),
                                ];
                            })
                            ->toArray(),
        ]);
    }

    /**
     * Handle the resource registered event.
     */
    public function boot(Root $root): void
    {
        $root->routes(function (Router $router): void {
            $this->registerRoutes($router);
        });
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $request = App::make('request');

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveWidgets($request)->registerRoutes($router);
            $this->resolveExtracts($request)->registerRoutes($router);
            $this->resolveActions($request)->registerRoutes($router);
            $router->prefix("{{$this->getRouteKeyName()}}")->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($router);
            });
        });
    }

    /**
     * Register the route constraints.
     */
    public function registerRouteConstraints(Router $router): void
    {
        $router->bind($this->getRouteKeyName(), function (string $id) use ($router): Model {
            return $id === 'create'
                ? $this->getModelInstance()
                : $this->resolveRouteBinding($router->getCurrentRequest(), $id);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', [ResourceController::class, 'index']);
        $router->get('/create', [ResourceController::class, 'create']);
        $router->post('/', [ResourceController::class, 'store']);
        $router->get("{{$this->getRouteKeyName()}}", [ResourceController::class, 'show']);
        $router->get("{{$this->getRouteKeyName()}}/edit", [ResourceController::class, 'edit']);
        $router->patch("{{$this->getRouteKeyName()}}", [ResourceController::class, 'update']);
        $router->delete("{{$this->getRouteKeyName()}}", [ResourceController::class, 'destroy']);

        if ($this->isSoftDeletable()) {
            $router->post("{{$this->getRouteKeyName()}}/restore", [ResourceController::class, 'restore']);
        }
    }
}
