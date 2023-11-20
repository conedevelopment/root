<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Interfaces\Form;
use Cone\Root\Root;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Relation extends Field implements Form
{
    use AsForm;
    use ResolvesActions;
    use ResolvesFilters;
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The relation name on the model.
     */
    protected Closure|string $relation;

    /**
     * The searchable columns.
     */
    protected array $searchableColumns = ['id'];

    /**
     * The sortable column.
     */
    protected string $sortableColumn = 'id';

    /**
     * Indicates if the field should be nullable.
     */
    protected bool $nullable = false;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.select';

    /**
     * The display resolver callback.
     */
    protected ?Closure $displayResolver = null;

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * The option group resolver.
     */
    protected string|Closure|null $groupResolver = null;

    /**
     * Indicates whether the relation is a sub resource.
     */
    protected bool $asSubResource = false;

    /**
     * The query scopes.
     */
    protected static array $scopes = [];

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->relation = $relation ?: $this->getModelAttribute();
    }

    /**
     * Add a new scope for the relation query.
     */
    public static function scopeQuery(Closure $callback): void
    {
        static::$scopes[static::class][] = $callback;
    }

    /**
     * Get the relation instance.
     */
    public function getRelation(Model $model): EloquentRelation
    {
        if ($this->relation instanceof Closure) {
            return call_user_func_array($this->relation, [$model]);
        }

        return call_user_func([$model, $this->relation]);
    }

    /**
     * Get the related model name.
     */
    public function getRelatedName(): string
    {
        return __(Str::of($this->getModelAttribute())->singular()->headline()->value());
    }

    /**
     * Get the relation name.
     */
    public function getRelationName(): string
    {
        return $this->relation instanceof Closure
            ? Str::afterLast($this->getModelAttribute(), '.')
            : $this->relation;
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return str_replace('.', '-', $this->getRequestKey());
    }

    /**
     * Get the route parameter name.
     */
    public function getRouteParameterName(): string
    {
        return 'field';
    }

    /**
     * Set the as subresource attribute.
     */
    public function asSubResource(bool $value = true): static
    {
        $this->asSubResource = $value;

        return $this;
    }

    /**
     * Determine if the relation is a subresource.
     */
    public function isSubResource(): bool
    {
        return $this->asSubResource;
    }

    /**
     * Set the nullable attribute.
     */
    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Determine if the field is nullable.
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool|Closure $value = true, array $columns = ['id']): static
    {
        $this->searchableColumns = $columns;

        return parent::searchable($value);
    }

    /**
     * Get the searchable columns.
     */
    public function getSearchableColumns(): array
    {
        return $this->searchableColumns;
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool|Closure $value = true, string $column = 'id'): static
    {
        $this->sortableColumn = $column;

        return parent::sortable($value);
    }

    /**
     * Get the sortable columns.
     */
    public function getSortableColumn(): string
    {
        return $this->sortableColumn;
    }

    /**
     * Set the display resolver.
     */
    public function display(Closure|string $callback): static
    {
        if (is_string($callback)) {
            $callback = static function (Model $model) use ($callback) {
                return $model->getAttribute($callback);
            };
        }

        $this->displayResolver = $callback;

        return $this;
    }

    /**
     * Resolve the display format or the query result.
     */
    public function resolveDisplay(Model $related): mixed
    {
        if (is_null($this->displayResolver)) {
            $this->display($related->getKeyName());
        }

        return call_user_func_array($this->displayResolver, [$related]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Model $model): mixed
    {
        $name = $this->getRelationName();

        if ($this->relation instanceof Closure && ! $model->relationLoaded($name)) {
            $model->setRelation($name, call_user_func_array($this->relation, [$model])->getResults());
        }

        return $model->getAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model): mixed {
                $default = $this->getValue($model);

                return Collection::wrap($default)->map(function (Model $related) use ($request): mixed {
                    $resource = Root::instance()->resources->forModel($related);

                    $value = $this->resolveDisplay($related);

                    if (! is_null($resource) && $related->exists && $request->user()->can('view', $related)) {
                        $value = sprintf('<a href="%s">%s</a>', $resource->modelUrl($related), $value);
                    }

                    return $value;
                })->join(', ');
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Set the query resolver.
     */
    public function withRelatableQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the related model's eloquent query.
     */
    public function resolveRelatableQuery(Request $request, Model $model): Builder
    {
        $query = $this->getRelation($model)->getRelated()->newQuery();

        foreach (static::$scopes[static::class] ?? [] as $scope) {
            $query = call_user_func_array($scope, [$request, $query, $model]);
        }

        return $query->when(! is_null($this->queryResolver), function (Builder $query) use ($request, $model): Builder {
            return call_user_func_array($this->queryResolver, [$request, $query, $model]);
        });
    }

    /**
     * Set the group resolver attribute.
     */
    public function groupOptionsBy(string|Closure $key): static
    {
        $this->groupResolver = $key;

        return $this;
    }

    /**
     * Resolve the options for the field.
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return $this->resolveRelatableQuery($request, $model)
            ->get()
            ->when(! is_null($this->groupResolver), function (Collection $collection) use ($request, $model): Collection {
                return $collection->groupBy($this->groupResolver)
                    ->map(function (Collection $group, string $key) use ($request, $model): array {
                        return [
                            'label' => $key,
                            'options' => $group->map(function (Model $related) use ($request, $model): array {
                                return $this->toOption($request, $model, $related);
                            })->all(),
                        ];
                    });
            }, function (Collection $collection) use ($request, $model): Collection {
                return $collection->map(function (Model $related) use ($request, $model): array {
                    return $this->toOption($request, $model, $related);
                });
            })
            ->toArray();
    }

    /**
     * Make a new option instance.
     */
    public function newOption(Model $related, string $label): Option
    {
        return new Option($related->getKey(), $label);
    }

    /**
     * Get the per page options.
     */
    public function getPerPageOptions(): array
    {
        return [5, 10, 15, 25];
    }

    /**
     * Paginate the given query.
     */
    public function paginate(Request $request, Model $model): LengthAwarePaginator
    {
        return tap($this->getRelation($model), function (EloquentRelation $relation) use ($request): void {
            $this->resolveFilters($request)->apply($request, $relation->getQuery())->latest();
        })->paginate($request->input('per_page'))->withQueryString();
    }

    /**
     * Map a related model.
     */
    public function mapRelated(Request $request, Model $model, Model $related): array
    {
        return [
            'id' => $related->getKey(),
            'url' => $this->relatedUrl($model, $related),
            'model' => $related,
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('relation.index')
                ->mapToDisplay($request, $related),
        ];
    }

    /**
     * Get the model URL.
     */
    public function modelUrl(Model $model): string
    {
        return str_replace('{resourceModel}', $model->getKey(), $this->getUri());
    }

    /**
     * Get the related URL.
     */
    public function relatedUrl(Model $model, Model $related): string
    {
        return sprintf('%s/%s', $this->modelUrl($model), $related->getKey());
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, Model $model, string $id): Model
    {
        return $this->getRelation($model)->findOrFail($id);
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveActions($request)->registerRoutes($request, $router);

            $router->prefix('{resourceRelation}')->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
            });
        });
    }

    /**
     * Register the routes.
     */
    public function routes(Router $router): void
    {
        if ($this->isSubResource()) {
            $router->get('/', [RelationController::class, 'index']);
            $router->get('/create', [RelationController::class, 'create']);
            $router->get('/{resourceRelation}', [RelationController::class, 'show']);
            $router->post('/', [RelationController::class, 'store']);
            $router->get('/{resourceRelation}/edit', [RelationController::class, 'edit']);
            $router->patch('/{resourceRelation}', [RelationController::class, 'update']);
            $router->delete('/{resourceRelation}', [RelationController::class, 'destroy']);
        }
    }

    /**
     * Get the option representation of the model and the related model.
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        $value = $this->resolveValue($request, $model);

        if (is_null($value)) {
            return [];
        }

        return $this->newOption($related, $this->resolveDisplay($related))
            ->selected($value instanceof Model ? $value->is($related) : $value->contains($related))
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'nullable' => $this->isNullable(),
            'options' => $this->resolveOptions($request, $model),
        ]);
    }

    /**
     * Get the sub resource representation of the relation.
     */
    public function toSubResource(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'key' => $this->modelAttribute,
            'url' => $this->modelUrl($model),
        ]);
    }

    /**
     * Get the index representation of the relation.
     */
    public function toIndex(Request $request, Model $model): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'title' => $this->label,
            'actions' => $this->resolveActions($request)
                ->authorized($request, $model)
                ->visible('relation.index')
                ->mapToForms($request, $model),
            'data' => $this->paginate($request, $model)->through(function (Model $related) use ($request, $model): array {
                return $this->mapRelated($request, $model, $related);
            }),
            'perPageOptions' => $this->getPerPageOptions(),
            'filters' => $this->resolveFilters($request)
                ->authorized($request)
                ->renderable()
                ->map(static function (RenderableFilter $filter) use ($request, $model): array {
                    return $filter->toField()->toInput($request, $model);
                })
                ->all(),
            'activeFilters' => $this->resolveFilters($request)->active($request)->count(),
        ]);
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request, Model $model): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'title' => __('Create :model', ['model' => $this->getRelatedName()]),
            'model' => $related = $this->getRelation($model)->getRelated(),
            'action' => $this->modelUrl($model),
            'method' => 'POST',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('relation.create')
                ->mapToInputs($request, $related),
        ]);
    }

    /**
     * Get the edit representation of the relation.
     */
    public function toShow(Request $request, Model $model, Model $related): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'title' => sprintf('%s #%s', $this->getRelatedName(), $related->getKey()),
            'model' => $related,
            'action' => $this->relatedUrl($model, $related),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('relation.show')
                ->mapToDisplay($request, $related),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $related)
                ->visible('relation.show')
                ->mapToForms($request, $related),
        ]);
    }

    /**
     * Get the edit representation of the relation.
     */
    public function toEdit(Request $request, Model $model, Model $related): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'title' => __('Edit :model', ['model' => sprintf('%s #%s', $this->getRelatedName(), $related->getKey())]),
            'model' => $related,
            'action' => $this->relatedUrl($model, $related),
            'method' => 'PATCH',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('relation.update')
                ->mapToInputs($request, $related),
        ]);
    }
}
