<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\Action;
use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Filters\TrashStatus;
use Cone\Root\Interfaces\Form;
use Cone\Root\Root;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class Resource implements Arrayable, Form
{
    use AsForm;
    use Authorizable;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
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
     * Boot the resource.
     */
    public function boot(Root $root): void
    {
        $root->routes(function (Router $router) use ($root): void {
            $this->registerRoutes($root->app['request'], $router);
        });
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
        return $this->resolveQuery($request)->when(
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
        return sprintf('%s/%s', $this->getUri(), $model->exists ? $model->getRouteKey() : '');
    }

    /**
     * Define the filters for the object.
     */
    public function filters(Request $request): array
    {
        $fields = $this->resolveFields($request)->authorized($request);

        $searchables = $fields->searchable($request);

        $sortables = $fields->sortable($request);

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
    }

    /**
     * Handle the callback for the action resolution.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->setQuery($this->resolveFilteredQuery($request));
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
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->resolveFilteredQuery($request)
            ->latest()
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->through(function (Model $model) use ($request): array {
                return [
                    'id' => $model->getKey(),
                    'url' => $this->modelUrl($model),
                    'model' => $model,
                    'fields' => $this->resolveFields($request)
                        ->subResource(false)
                        ->authorized($request, $model)
                        ->visible('index')
                        ->mapToDisplay($request, $model),
                ];
            });
    }

    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request, Model $model): void
    {
        $this->validateFormRequest($request, $model);

        $this->resolveFields($request)
            ->authorized($request, $model)
            ->visible($request->method() === 'POST' ? 'create' : 'update')
            ->persist($request, $model);

        $model->save();
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveActions($request)->registerRoutes($request, $router);

            $router->prefix('{resourceModel}')->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
            });
        });
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
            'title' => $this->getName(),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $this->getModelInstance())
                ->visible('index')
                ->mapToForms($request, $this->getModelInstance()),
            'data' => $this->paginate($request),
            'widgets' => $this->resolveWidgets($request)
                ->authorized($request)
                ->visible('index')
                ->toArray(),
            'perPageOptions' => $this->getPerPageOptions(),
            'filters' => $this->resolveFilters($request)
                ->authorized($request)
                ->renderable()
                ->map(function (RenderableFilter $filter) use ($request): array {
                    return $filter->toField()->toInput($request, $this->getModelInstance());
                })
                ->all(),
            'activeFilters' => $this->resolveFilters($request)->active($request)->count(),
        ]);
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return array_merge($this->toArray(), [
            'title' => __('Create :model', ['model' => $this->getModelName()]),
            'model' => $model = $this->getModelInstance(),
            'action' => $this->getUri(),
            'method' => 'POST',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('create')
                ->mapToInputs($request, $model),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toShow(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'title' => sprintf('%s #%s', $this->getModelName(), $model->getKey()),
            'model' => $model,
            'action' => $this->modelUrl($model),
            'method' => 'PATCH',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('show')
                ->mapToDisplay($request, $model),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $model)
                ->visible('show')
                ->mapToForms($request, $model),
            'widgets' => $this->resolveWidgets($request)
                ->authorized($request, $model)
                ->visible('show')
                ->toArray(),
            'relations' => $this->resolveFields($request)
                ->subResource()
                ->authorized($request, $model)
                ->map(static function (Relation $relation) use ($request, $model): array {
                    return $relation->toSubResource($request, $model);
                }),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'title' => __('Edit :model', ['model' => sprintf('%s #%s', $this->getModelName(), $model->getKey())]),
            'model' => $model,
            'action' => $this->modelUrl($model),
            'method' => 'PATCH',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $model)
                ->visible('update')
                ->mapToInputs($request, $model),
        ]);
    }
}
