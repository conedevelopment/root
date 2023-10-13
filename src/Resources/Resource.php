<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\Action;
use Cone\Root\Fields\Field;
use Cone\Root\Filters\Filter;
use Cone\Root\Interfaces\Form;
use Cone\Root\Interfaces\Table;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesColumns;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Resource implements Arrayable, Form, Table
{
    use AsForm;
    use Authorizable;
    use ResolvesActions;
    use ResolvesColumns;
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
     * Get the resource URL.
     */
    public function getUrl(): string
    {
        return URL::route('root.resource.index', $this->getUriKey());
    }

    /**
     * Get the URL for the given model.
     */
    public function modelUrl(Model $model): string
    {
        return URL::route($model->exists ? 'root.resource.update' : 'root.resource.store', [$this->getUriKey(), $model]);
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setApiUri(sprintf('/root/api/%s/fields/%s', $this->getKey(), $field->getUriKey()));
        $field->setAttribute('form', $this->getKey());
        $field->resolveErrorsUsing(function (Request $request): MessageBag {
            return $this->errors($request);
        });
    }

    /**
     * Handle the callback for the action resolution.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->setQuery($this->resolveFilteredQuery($request));
        $action->setApiUri(sprintf('/root/api/%s/actions/%s', $this->getKey(), $action->getUriKey()));
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
                    'cells' => $this->resolveColumns($request)->mapToCells($request, $model),
                ];
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
            'url' => $this->getUrl(),
        ];
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return array_merge($this->toArray(), [
            'title' => $this->getName(),
            'columns' => $this->resolveColumns($request)->mapToHeads($request),
            'actions' => $this->resolveActions($request)->mapToTableComponents($request),
            'data' => $this->paginate($request),
            'widgets' => $this->resolveWidgets($request)->all(),
            'perPageOptions' => $this->getPerPageOptions(),
            'filters' => $this->resolveFilters($request)
                ->renderable()
                ->map(function (Filter $filter) use ($request): array {
                    return $filter->toField()->toFormComponent($request, $this->getModelInstance());
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
            'action' => $this->getUrl(),
            'method' => 'POST',
            'fields' => $this->resolveFields($request)->mapToFormComponents($request, $model),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'title' => '',
            'model' => $model,
            'action' => $this->modelUrl($model),
            'method' => 'PATCH',
            'fields' => $this->resolveFields($request)->mapToFormComponents($request, $model),
        ]);
    }
}
