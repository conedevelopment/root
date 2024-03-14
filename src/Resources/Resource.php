<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Filters\TrashStatus;
use Cone\Root\Http\Middleware\Authorize;
use Cone\Root\Interfaces\Form;
use Cone\Root\Root;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Metric;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Throwable;

abstract class Resource implements Arrayable, Form
{
    use AsForm;
    use Authorizable;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
        RegistersRoutes::routeMatched as __routeMatched;
    }
    use ResolvesActions;
    use ResolvesFilters;
    use ResolvesWidgets;

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
    protected string $icon = 'archive';

    /**
     * The group for the resource.
     */
    protected string $group = 'General';

    /**
     * Boot the resource.
     */
    public function boot(Root $root): void
    {
        $root->routes(function (Router $router) use ($root): void {
            $this->registerRoutes($root->app['request'], $router);
        });

        $root->navigation->location('sidebar')->new(
            $this->getUri(),
            $this->getName(),
            ['icon' => $this->getIcon(), 'group' => __($this->group)],
        );
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
     * Get the route parameter name.
     */
    public function getRouteParameterName(): string
    {
        return '_resource';
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
     * Resolve the ability.
     */
    public function resolveAbility(string $ability, Request $request, Model $model, ...$arguments): bool
    {
        $policy = $this->getPolicy();

        return is_null($policy)
            || ! method_exists($policy, $ability)
            || call_user_func_array([$policy, $ability], [$request->user(), $model, ...$arguments]);
    }

    /**
     * Map the resource abilities.
     */
    public function mapResourceAbilities(Request $request): array
    {
        return [
            'viewAny' => $this->resolveAbility('viewAny', $request, $this->getModelInstance()),
            'create' => $this->resolveAbility('create', $request, $this->getModelInstance()),
        ];
    }

    /**
     * Map the model abilities.
     */
    public function mapModelAbilities(Request $request, Model $model): array
    {
        return [
            'view' => $this->resolveAbility('view', $request, $model),
            'update' => $this->resolveAbility('update', $request, $model),
            'restore' => $this->resolveAbility('restore', $request, $model),
            'delete' => $this->resolveAbility('delete', $request, $model),
            'forceDelete' => $this->resolveAbility('forceDelete', $request, $model),
        ];
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
     * Resolve the filtered query for the given request.
     */
    public function resolveFilteredQuery(Request $request): Builder
    {
        return $this->resolveFilters($request)->apply($request, $this->resolveQuery($request));
    }

    /**
     * Resolve the route binding query.
     */
    public function resolveRouteBindingQuery(Request $request): Builder
    {
        return $this->resolveQuery($request)
            ->withoutEagerLoads()
            ->when(
                $this->isSoftDeletable(),
                static function (Builder $query): Builder {
                    return $query->withTrashed();
                }
            );
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->resolveRouteBindingQuery($request)->findOrFail($id);
    }

    /**
     * Determine if the model soft deletable.
     */
    public function isSoftDeletable(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->getModel()));
    }

    /**
     * Get the URL for the given model.
     */
    public function modelUrl(Model $model): string
    {
        return sprintf('%s/%s', $this->getUri(), $model->exists ? $model->getKey() : '');
    }

    /**
     * Get the title for the model.
     */
    public function modelTitle(Model $model): string
    {
        return (string) $model->getKey();
    }

    /**
     * Define the filters for the object.
     */
    public function filters(Request $request): array
    {
        $fields = $this->resolveFields($request)->authorized($request);

        $searchables = $fields->searchable();

        $sortables = $fields->sortable();

        return array_values(array_filter([
            $searchables->isNotEmpty() ? new Search($searchables) : null,
            $sortables->isNotEmpty() ? new Sort($sortables) : null,
            $this->isSoftDeletable() ? new TrashStatus() : null,
        ]));
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setAttribute('form', $this->getKey());
        $field->resolveErrorsUsing(fn (Request $request): MessageBag => $this->errors($request));

        if ($field instanceof Relation) {
            $field->resolveRouteKeyNameUsing(function () use ($field): string {
                return Str::of($field->getRelationName())->singular()->ucfirst()->prepend($this->getKey())->value();
            });
        }
    }

    /**
     * Handle the callback for the filter resolution.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        $filter->setKey(sprintf('%s_%s', $this->getKey(), $filter->getKey()));
    }

    /**
     * Handle the callback for the action resolution.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->setQuery($this->resolveFilteredQuery($request));
    }

    /**
     * Handle the callback for the widget resolution.
     */
    protected function resolveWidget(Request $request, Widget $widget): void
    {
        if ($widget instanceof Metric) {
            $widget->setQuery($this->resolveFilteredQuery($request)->clone()->withoutEagerLoads());
        }
    }

    /**
     * Get the per page options.
     */
    public function getPerPageOptions(): array
    {
        return Collection::make([$this->getModelInstance()->getPerPage()])
            ->merge([15, 25, 50, 100])
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get the per page key.
     */
    public function getPerPageKey(): string
    {
        return sprintf('%s_per_page', $this->getKey());
    }

    /**
     * Get the sort key.
     */
    public function getSortKey(): string
    {
        return sprintf('%s_sort', $this->getKey());
    }

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->resolveFilteredQuery($request)
            ->latest()
            ->paginate($request->input($this->getPerPageKey()))
            ->withQueryString()
            ->through(function (Model $model) use ($request): array {
                return $this->mapModel($request, $model);
            });
    }

    /**
     * Map the model.
     */
    public function mapModel(Request $request, Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'url' => $this->modelUrl($model),
            'model' => $model,
            'abilities' => $this->mapModelAbilities($request, $model),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('index')
                ->mapToDisplay($request, $model),
        ];
    }

    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request, Model $model): void
    {
        $this->validateFormRequest($request, $model);

        try {
            DB::beginTransaction();

            $this->resolveFields($request)
                ->authorized($request, $model)
                ->visible($request->isMethod('POST') ? 'create' : 'update')
                ->persist($request, $model);

            $this->saving($request, $model);

            $model->save();

            $this->saved($request, $model);

            DB::commit();
        } catch (Throwable $exception) {
            report($exception);

            DB::rollBack();

            throw new SaveFormDataException($exception->getMessage());
        }
    }

    /**
     * Handle the saving form event.
     */
    public function saving(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the saved form event.
     */
    public function saved(Request $request, Model $model): void
    {
        //
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->group([
            'prefix' => $this->getUriKey(),
            'middleware' => $this->getRouteMiddleware(),
        ], function (Router $router) use ($request): void {
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveWidgets($request)->registerRoutes($request, $router);

            $router->prefix('{resourceModel}')->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
            });
        });
    }

    /**
     * Get the route middleware for the registered routes.
     */
    public function getRouteMiddleware(): array
    {
        return [
            Authorize::class.':_resource',
        ];
    }

    /**
     * Handle the route matched event.
     */
    public function routeMatched(RouteMatched $event): void
    {
        $event->route->defaults('resource', $this->getKey());

        $controller = $event->route->getController();

        $controller->middleware($this->getRouteMiddleware());

        if (! is_null($this->getPolicy())) {
            $controller->authorizeResource($this->getModel(), 'resourceModel');
        }

        $this->__routeMatched($event);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'icon' => $this->getIcon(),
            'key' => $this->getKey(),
            'model' => $this->getModel(),
            'modelName' => $this->getModelName(),
            'name' => $this->getName(),
            'uriKey' => $this->getUriKey(),
            'url' => $this->getUri(),
        ];
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return array_merge($this->toArray(), [
            'template' => 'root::resources.index',
            'title' => $this->getName(),
            'standaloneActions' => $this->resolveActions($request)
                ->authorized($request, $model = $this->getModelInstance())
                ->standalone()
                ->mapToForms($request, $model),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $model = $this->getModelInstance())
                ->visible('index')
                ->standalone(false)
                ->mapToForms($request, $model),
            'data' => $this->paginate($request),
            'widgets' => $this->resolveWidgets($request)
                ->authorized($request)
                ->visible('index')
                ->mapToDisplay($request),
            'perPageOptions' => $this->getPerPageOptions(),
            'perPageKey' => $this->getPerPageKey(),
            'sortKey' => $this->getSortKey(),
            'filters' => $this->resolveFilters($request)
                ->authorized($request)
                ->renderable()
                ->map(function (RenderableFilter $filter) use ($request, $model): array {
                    return $filter->toField()->toInput($request, $model);
                })
                ->all(),
            'activeFilters' => $this->resolveFilters($request)->active($request)->count(),
            'url' => $this->getUri(),
            'abilities' => $this->mapResourceAbilities($request),
        ]);
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return array_merge($this->toArray(), [
            'template' => 'root::resources.form',
            'title' => __('Create :resource', ['resource' => $this->getModelName()]),
            'model' => $model = $this->getModelInstance(),
            'action' => $this->getUri(),
            'method' => 'POST',
            'uploads' => $this->hasFileField($request),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('create')
                ->mapToInputs($request, $model),
            'abilities' => $this->mapResourceAbilities($request),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toShow(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'template' => 'root::resources.show',
            'title' => sprintf('%s: %s', $this->getModelName(), $this->modelTitle($model)),
            'model' => $model,
            'action' => $this->modelUrl($model),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('show')
                ->mapToDisplay($request, $model),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $model)
                ->visible('show')
                ->standalone(false)
                ->mapToForms($request, $model),
            'widgets' => $this->resolveWidgets($request)
                ->authorized($request, $model)
                ->visible('show')
                ->mapToDisplay($request),
            'relations' => $this->resolveFields($request)
                ->subResource()
                ->authorized($request, $model)
                ->map(static function (Relation $relation) use ($request, $model): array {
                    return array_merge($relation->toSubResource($request, $model), [
                        'url' => trim(sprintf('%s?%s', $relation->modelUrl($model), $request->getQueryString()), '?'),
                    ]);
                }),
            'abilities' => array_merge(
                $this->mapResourceAbilities($request),
                $this->mapModelAbilities($request, $model)
            ),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'template' => 'root::resources.form',
            'title' => __('Edit :resource: :model', ['resource' => $this->getModelName(), 'model' => $this->modelTitle($model)]),
            'model' => $model,
            'action' => $this->modelUrl($model),
            'method' => 'PATCH',
            'uploads' => $this->hasFileField($request),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('update')
                ->mapToInputs($request, $model),
            'abilities' => array_merge(
                $this->mapResourceAbilities($request),
                $this->mapModelAbilities($request, $model)
            ),
        ]);
    }
}
