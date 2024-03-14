<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Http\Middleware\Authorize;
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
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Throwable;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\Relation
 */
abstract class Relation extends Field implements Form
{
    use AsForm;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
        RegistersRoutes::routeMatched as __routeMatched;
    }
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;

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
     * The relations to eager load on every query.
     */
    protected array $with = [];

    /**
     * The relations to eager load on every query.
     */
    protected array $withCount = [];

    /**
     * The query scopes.
     */
    protected static array $scopes = [];

    /**
     * The route key resolver.
     */
    protected ?Closure $routeKeyNameResolver = null;

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
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
     *
     * @phpstan-return TRelation
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
     * Set the route key name resolver.
     */
    public function resolveRouteKeyNameUsing(Closure $callback): static
    {
        $this->routeKeyNameResolver = $callback;

        return $this;
    }

    /**
     * Get the related model's route key name.
     */
    public function getRouteKeyName(): string
    {
        $callback = is_null($this->routeKeyNameResolver)
            ? function (): string {
                return Str::of($this->getRelationName())->singular()->ucfirst()->prepend('relation')->value();
            }
        : $this->routeKeyNameResolver;

        return call_user_func($callback);
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isSearchable(): bool
    {
        if ($this->isSubResource()) {
            return false;
        }

        return parent::isSearchable();
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
     * {@inheritdoc}
     */
    public function isSortable(): bool
    {
        if ($this->isSubResource()) {
            return false;
        }

        return parent::isSortable();
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
    public function resolveDisplay(Model $related): ?string
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
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model): mixed {
                $default = $this->getValue($model);

                return Collection::wrap($default)->map(function (Model $related) use ($model, $request): mixed {
                    return $this->formatRelated($request, $model, $related);
                })->filter()->join(', ');
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Format the related model.
     */
    public function formatRelated(Request $request, Model $model, Model $related): ?string
    {
        $resource = Root::instance()->resources->forModel($related);

        $value = $this->resolveDisplay($related);

        if (! is_null($resource) && $related->exists && $resource->resolveAbility('view', $request, $related)) {
            $value = sprintf('<a href="%s" data-turbo-frame="_top">%s</a>', $resource->modelUrl($related), $value);
        }

        return $value;
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
        ]));
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if ($this->isSubResource()) {
            $field->setAttribute('form', $this->modelAttribute);
            $field->resolveErrorsUsing(fn (Request $request): MessageBag => $this->errors($request));
        } else {
            $field->setAttribute('form', $this->getAttribute('form'));
            $field->resolveErrorsUsing($this->errorsResolver);
        }

        if ($field instanceof Relation) {
            $field->resolveRouteKeyNameUsing(function () use ($field): string {
                return Str::of($field->getRelationName())->singular()->ucfirst()->prepend($this->getRouteKeyName())->value();
            });
        }
    }

    /**
     * Handle the callback for the filter resolution.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        $filter->setKey(sprintf('%s_%s', $this->getRequestKey(), $filter->getKey()));
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
        $query = $this->getRelation($model)
            ->getRelated()
            ->newQuery()
            ->with($this->with)
            ->withCount($this->withCount);

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
     * Get the per page key.
     */
    public function getPerPageKey(): string
    {
        return sprintf('%s_per_page', $this->getRequestKey());
    }

    /**
     * Get the sort key.
     */
    public function getSortKey(): string
    {
        return sprintf('%s_sort', $this->getRequestKey());
    }

    /**
     * The relations to be eagerload.
     */
    public function with(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * The relation counts to be eagerload.
     */
    public function withCount(array $withCount): static
    {
        $this->withCount = $withCount;

        return $this;
    }

    /**
     * Paginate the given query.
     */
    public function paginate(Request $request, Model $model): LengthAwarePaginator
    {
        $relation = $this->getRelation($model);

        return $this->resolveFilters($request)
            ->apply($request, $relation->getQuery())
            ->with($this->with)
            ->withCount($this->withCount)
            ->latest()
            ->paginate($request->input($this->getPerPageKey(), $request->isTurboFrameRequest() ? 5 : $relation->getRelated()->getPerPage()))
            ->withQueryString();
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
                ->visible('index')
                ->mapToDisplay($request, $related),
            'abilities' => $this->mapRelatedAbilities($request, $model, $related),
        ];
    }

    /**
     * Get the model URL.
     */
    public function modelUrl(Model $model): string
    {
        return str_replace('{resourceModel}', $model->exists ? (string) $model->getKey() : 'create', $this->getUri());
    }

    /**
     * Get the related URL.
     */
    public function relatedUrl(Model $model, Model $related): string
    {
        return sprintf('%s/%s', $this->modelUrl($model), $related->getKey());
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
     * Handle the saved form event.
     */
    public function saved(Request $request, Model $model): void
    {
        //
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()))->findOrFail($id);
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveActions($request)->registerRoutes($request, $router);

            $router->prefix("{{$this->getRouteKeyName()}}")->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
            });
        });

        $this->registerRouteConstraints($request, $router);

        $this->routesRegistered($request);
    }

    /**
     * Get the route middleware for the registered routes.
     */
    public function getRouteMiddleware(): array
    {
        return [
            sprintf('%s:field,resourceModel,%s', Authorize::class, $this->getRouteKeyName()),
        ];
    }

    /**
     * Handle the routes registered event.
     */
    protected function routesRegistered(Request $request): void
    {
        Root::instance()->breadcrumbs->patterns([
            $this->getUri() => $this->label,
            sprintf('%s/create', $this->getUri()) => __('Add'),
            sprintf('%s/{%s}', $this->getUri(), $this->getRouteKeyName()) => function (Request $request): string {
                return $this->resolveDisplay($request->route($this->getRouteKeyName()));
            },
            sprintf('%s/{%s}/edit', $this->getUri(), $this->getRouteKeyName()) => __('Edit'),
        ]);
    }

    /**
     * Handle the route matched event.
     */
    public function routeMatched(RouteMatched $event): void
    {
        $this->__routeMatched($event);

        $controller = $event->route->getController();

        $controller->middleware($this->getRouteMiddleware());

        $middleware = function (Request $request, Closure $next) use ($event): mixed {
            $ability = match ($event->route->getActionMethod()) {
                'index' => 'viewAny',
                'show' => 'view',
                'create' => 'create',
                'store' => 'create',
                'edit' => 'update',
                'update' => 'update',
                'destroy' => 'delete',
                default => $event->route->getActionMethod(),
            };

            Gate::allowIf($this->resolveAbility(
                $ability, $request, $request->route('resourceModel'), $request->route($this->getRouteParameterName())
            ));

            return $next($request);
        };

        $controller->middleware([$middleware]);
    }

    /**
     * Resolve the ability.
     */
    public function resolveAbility(string $ability, Request $request, Model $model, ...$arguments): bool
    {
        $policy = Gate::getPolicyFor($model);

        $ability .= Str::of($this->getModelAttribute())->singular()->studly()->value();

        return is_null($policy)
            || ! method_exists($policy, $ability)
            || call_user_func_array([$policy, $ability], [$request->user(), $model, ...$arguments]);
    }

    /**
     * Map the relation abilities.
     */
    public function mapRelationAbilities(Request $request, Model $model): array
    {
        return [
            'viewAny' => $this->resolveAbility('viewAny', $request, $model),
            'create' => $this->resolveAbility('create', $request, $model),
        ];
    }

    /**
     * Map the related model abilities.
     */
    public function mapRelatedAbilities(Request $request, Model $model, Model $related): array
    {
        return [
            'view' => $this->resolveAbility('view', $request, $model, $related),
            'update' => $this->resolveAbility('update', $request, $model, $related),
            'restore' => $this->resolveAbility('restore', $request, $model, $related),
            'delete' => $this->resolveAbility('delete', $request, $model, $related),
            'forceDelete' => $this->resolveAbility('forceDelete', $request, $model, $related),
        ];
    }

    /**
     * Register the routes.
     */
    public function routes(Router $router): void
    {
        if ($this->isSubResource()) {
            $router->get('/', [RelationController::class, 'index']);
            $router->get('/create', [RelationController::class, 'create']);
            $router->get("/{{$this->getRouteKeyName()}}", [RelationController::class, 'show']);
            $router->post('/', [RelationController::class, 'store']);
            $router->get("/{{$this->getRouteKeyName()}}/edit", [RelationController::class, 'edit']);
            $router->patch("/{{$this->getRouteKeyName()}}", [RelationController::class, 'update']);
            $router->delete("/{{$this->getRouteKeyName()}}", [RelationController::class, 'destroy']);
        }
    }

    /**
     * Register the route constraints.
     */
    public function registerRouteConstraints(Request $request, Router $router): void
    {
        $router->bind($this->getRouteKeyName(), function (string $id) use ($request): Model {
            return $id === 'create'
                ? $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()))->make()
                : $this->resolveRouteBinding($request, $id);
        });
    }

    /**
     * Get the option representation of the model and the related model.
     */
    public function toOption(Request $request, Model $model, Model $related): array
    {
        $value = $this->resolveValue($request, $model);

        return $this->newOption($related, $this->resolveDisplay($related))
            ->selected(! is_null($value) && ($value instanceof Model ? $value->is($related) : $value->contains($related)))
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
     * Get the sub resource representation of the relation
     */
    public function toSubResource(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'key' => $this->modelAttribute,
            'url' => $this->modelUrl($model),
            'modelName' => $this->getRelatedName(),
            'abilities' => $this->mapRelationAbilities($request, $model),
        ]);
    }

    /**
     * Get the index representation of the relation.
     */
    public function toIndex(Request $request, Model $model): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'template' => $request->isTurboFrameRequest() ? 'root::resources.relation' : 'root::resources.index',
            'title' => $this->label,
            'model' => $this->getRelation($model)->make(),
            'standaloneActions' => $this->resolveActions($request)
                ->authorized($request, $model)
                ->standalone()
                ->mapToForms($request, $model),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $model)
                ->visible('index')
                ->standalone(false)
                ->mapToForms($request, $model),
            'data' => $this->paginate($request, $model)->through(function (Model $related) use ($request, $model): array {
                return $this->mapRelated($request, $model, $related);
            }),
            'perPageOptions' => $this->getPerPageOptions(),
            'perPageKey' => $this->getPerPageKey(),
            'sortKey' => $this->getSortKey(),
            'filters' => $this->resolveFilters($request)
                ->authorized($request)
                ->renderable()
                ->map(static function (RenderableFilter $filter) use ($request, $model): array {
                    return $filter->toField()->toInput($request, $model);
                })
                ->all(),
            'activeFilters' => $this->resolveFilters($request)->active($request)->count(),
            'url' => $this->modelUrl($model),
        ]);
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request, Model $model): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.form',
            'title' => __('Create :model', ['model' => $this->getRelatedName()]),
            'model' => $related = $this->getRelation($model)->make(),
            'action' => $this->modelUrl($model),
            'uploads' => $this->hasFileField($request),
            'method' => 'POST',
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('create')
                ->mapToInputs($request, $related),
        ]);
    }

    /**
     * Get the edit representation of the
     */
    public function toShow(Request $request, Model $model, Model $related): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.show',
            'title' => $this->resolveDisplay($related),
            'model' => $related,
            'action' => $this->relatedUrl($model, $related),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('show')
                ->mapToDisplay($request, $related),
            'actions' => $this->resolveActions($request)
                ->authorized($request, $related)
                ->visible('show')
                ->standalone(false)
                ->mapToForms($request, $related),
            'abilities' => array_merge(
                $this->mapRelationAbilities($request, $model),
                $this->mapRelatedAbilities($request, $model, $related)
            ),
        ]);
    }

    /**
     * Get the edit representation of the
     */
    public function toEdit(Request $request, Model $model, Model $related): array
    {
        return array_merge($this->toSubResource($request, $model), [
            'template' => 'root::resources.form',
            'title' => __('Edit :model', ['model' => $this->resolveDisplay($related)]),
            'model' => $related,
            'action' => $this->relatedUrl($model, $related),
            'method' => 'PATCH',
            'uploads' => $this->hasFileField($request),
            'fields' => $this->resolveFields($request)
                ->subResource(false)
                ->authorized($request, $related)
                ->visible('update')
                ->mapToInputs($request, $related),
            'abilities' => array_merge(
                $this->mapRelationAbilities($request, $model),
                $this->mapRelatedAbilities($request, $model, $related)
            ),
        ]);
    }
}
