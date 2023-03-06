<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Relation extends Field
{
    use RegistersRoutes;

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
     * Indicates if the component is async.
     */
    protected bool $async = false;

    /**
     * The Vue component.
     */
    protected string $component = 'Select';

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
     * The query scopes.
     */
    protected static array $scopes = [];

    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $name = null, Closure|string $relation = null)
    {
        parent::__construct($label, $name);

        $this->relation = $relation ?: $this->name;
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
        return __(Str::of($this->name)->singular()->headline()->toString());
    }

    /**
     * Create a new method.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('relation_')->toString();
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
            $callback = static function (RootRequest $request, Model $model) use ($callback) {
                return $model->getAttribute($callback);
            };
        }

        $this->displayResolver = $callback;

        return $this;
    }

    /**
     * Resolve the display format or the query result.
     */
    public function resolveDisplay(RootRequest $request, Model $related): mixed
    {
        if (is_null($this->displayResolver)) {
            $this->display($related->getKeyName());
        }

        return call_user_func_array($this->displayResolver, [$request, $related]);
    }

    /**
     * Set the async attribute.
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        $this->component = $value ? 'AsyncSelect' : 'Select';

        return $this;
    }

    /**
     * Determine if the field is asnyc.
     */
    public function isAsync(): bool
    {
        return $this->async;
    }

    /**
     * Resolve the URI.
     */
    public function resolveUri(ResourceRequest $request): string
    {
        $uri = $this->getUri();

        foreach ($request->route()->originalParameters() as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return preg_replace('/\{.*?\}/', 'create', $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (RootRequest $request, Model $model, mixed $value): mixed {
                if ($value instanceof Model) {
                    return $value->getKey();
                } elseif ($value instanceof Collection) {
                    return $value->map->getKey()->toArray();
                }

                return $value;
            };
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(RootRequest $request, Model $model): mixed
    {
        if ($this->relation instanceof Closure) {
            $name = sprintf('__root_%s', $this->name);

            if (! $model->relationLoaded($name)) {
                $model->setRelation($name, call_user_func_array($this->relation, [$model])->getResults());
            }

            return $model->getAttribute($name);
        }

        return $model->getAttribute($this->relation);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (RootRequest $request, Model $model): mixed {
                $default = $this->getValue($request, $model);

                if ($default instanceof Model) {
                    return $this->resolveDisplay($request, $default);
                } elseif ($default instanceof Collection) {
                    $value = $default->map(function (Model $related) use ($request): mixed {
                        return $this->resolveDisplay($request, $related);
                    });

                    return $this->isAsync() && $request instanceof UpdateRequest
                        ? $value->toArray()
                        : $value->join(', ');
                }

                return $default;
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Set the query resolver.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the related model's eloquent query.
     */
    public function resolveQuery(RootRequest $request, Model $model): Builder
    {
        $query = $this->getRelation($model)->getRelated()->newQuery();

        foreach (static::$scopes[static::class] ?? [] as $scope) {
            $query = call_user_func_array($scope, [$request, $query, $model]);
        }

        if (! is_null($this->queryResolver)) {
            $query = call_user_func_array($this->queryResolver, [$request, $query, $model]);
        }

        return $query;
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
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        return $this->resolveQuery($request, $model)
                    ->get()
                    ->when(! is_null($this->groupResolver), function (Collection $collection) use ($request, $model): Collection {
                        return $collection->groupBy($this->groupResolver)->map(function ($group, $key) use ($request, $model): OptGroup {
                            $options = $group->map(function (Model $related) use ($request, $model): array {
                                return $this->mapOption($request, $model, $related);
                            });

                            return (new OptGroup($key))->options($options->toArray());
                        });
                    }, function (Collection $collection) use ($request, $model): Collection {
                        return $collection->map(function (Model $related) use ($request, $model): array {
                            return $this->mapOption($request, $model, $related);
                        });
                    })
                    ->toArray();
    }

    /**
     * Map the given option.
     */
    public function mapOption(RootRequest $request, Model $model, Model $related): array
    {
        return [
            'value' => $related->getKey(),
            'formatted_value' => $this->resolveDisplay($request, $related),
        ];
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
    {
        return $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()))->findOrFail($id);
    }

    /**
     * Register the router constrains.
     */
    public function registerRouterConstrains(RootRequest $request, Router $router): void
    {
        $router->bind($this->getRouteKeyName(), function (string $id, Route $route): Model {
            $request = App::make(ResourceRequest::class);

            $request->setRouteResolver(static function () use ($route): Route {
                return $route;
            });

            return $id === 'create'
                ? $this->getRelation($route->parentOfParameter($this->getRouteKeyName()))->getRelated()
                : $this->resolveRouteBinding($request, $id);
        });

        $router->pattern(
            $this->getRouteKeyName(),
            '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|create'
        );
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        if ($this->async) {
            $router->get('/', RelationController::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'nullable' => $this->isNullable(),
            'options' => $this->isAsync() ? [] : $this->resolveOptions($request, $model),
            'url' => $this->isAsync() ? $this->resolveUri($request) : null,
        ]);
    }
}
