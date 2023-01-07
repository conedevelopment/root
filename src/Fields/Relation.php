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
     *
     * @var \Closure|string
     */
    protected Closure|string $relation;

    /**
     * The searchable columns.
     *
     * @var array
     */
    protected array $searchableColumns = ['id'];

    /**
     * The sortable column.
     *
     * @var string
     */
    protected string $sortableColumn = 'id';

    /**
     * Indicates if the field should be nullable.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = false;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Select';

    /**
     * The display resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $displayResolver = null;

    /**
     * The query resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $queryResolver = null;

    /**
     * The query scopes.
     *
     * @var array
     */
    protected static array $scopes = [];

    /**
     * Create a new relation field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @param  \Closure|string|null  $relation
     * @return void
     */
    public function __construct(string $label, ?string $name = null, Closure|string $relation = null)
    {
        parent::__construct($label, $name);

        $this->relation = $relation ?: $this->name;
    }

    /**
     * Add a new scope for the relation query.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function scopeQuery(Closure $callback): void
    {
        static::$scopes[static::class][] = $callback;
    }

    /**
     * Get the relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Relations\Relation
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
     *
     * @return string
     */
    public function getRelatedName(): string
    {
        return __(Str::of($this->name)->singular()->headline()->toString());
    }

    /**
     * Create a new method.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('relation_')->toString();
    }

    /**
     * Set the nullable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Determine if the field is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set the searachable attribute.
     *
     * @param  bool|\Closure  $value
     * @param  array  $columns
     * @return $this
     */
    public function searchable(bool|Closure $value = true, array $columns = ['id']): static
    {
        $this->searchableColumns = $columns;

        return parent::searchable($value);
    }

    /**
     * Get the searchable columns.
     *
     * @return array
     */
    public function getSearchableColumns(): array
    {
        return $this->searchableColumns;
    }

    /**
     * Set the sortable attribute.
     *
     * @param  bool|\Closure  $value
     * @param  string  $column
     * @return $this
     */
    public function sortable(bool|Closure $value = true, string $column = 'id'): static
    {
        $this->sortableColumn = $column;

        return parent::sortable($value);
    }

    /**
     * Get the sortable columns.
     *
     * @return string
     */
    public function getSortableColumn(): string
    {
        return $this->sortableColumn;
    }

    /**
     * Set the display resolver.
     *
     * @param  \Closure|string  $callback
     * @return $this
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return mixed
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
     *
     * @param  bool  $value
     * @return $this
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        $this->component = $value ? 'AsyncSelect' : 'Select';

        return $this;
    }

    /**
     * Determine if the field is asnyc.
     *
     * @return bool
     */
    public function isAsync(): bool
    {
        return $this->async;
    }

    /**
     * Resolve the URI.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return string
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
            if ($model->relationLoaded($this->name)) {
                return $model->getAttribute($this->name);
            }

            return call_user_func_array($this->relation, [$model]);
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
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the related model's eloquent query.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Builder
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
     * Resolve the options for the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        return $this->resolveQuery($request, $model)
                    ->get()
                    ->map(function (Model $related) use ($request, $model): array {
                        return $this->mapOption($request, $model, $related);
                    })
                    ->toArray();
    }

    /**
     * Map the given option.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
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
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
    {
        return $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()))->findOrFail($id);
    }

    /**
     * Register the router constrains.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
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
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
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
