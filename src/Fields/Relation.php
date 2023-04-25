<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\RelationFieldController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Relation extends Field implements Routable
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
        return __(Str::of($this->name)->singular()->headline()->value());
    }

    /**
     * Get the relation name.
     */
    public function getRelationName(): string
    {
        return $this->relation instanceof Closure
            ? $this->name
            : $this->relation;
    }

    /**
     * Get the route key name.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('field_')->value();
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
            $callback = static function (Request $request, Model $model) use ($callback) {
                return $model->getAttribute($callback);
            };
        }

        $this->displayResolver = $callback;

        return $this;
    }

    /**
     * Resolve the display format or the query result.
     */
    public function resolveDisplay(Request $request, Model $related): mixed
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
     * {@inheritdoc}
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (Request $request, Model $model, mixed $value): mixed {
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
    public function getValue(Request $request, Model $model): mixed
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
                $default = $this->getValue($request, $model);

                if ($default instanceof Model) {
                    return $this->resolveDisplay($request, $default);
                } elseif ($default instanceof Collection) {
                    $value = $default->map(function (Model $related) use ($request): mixed {
                        return $this->resolveDisplay($request, $related);
                    });

                    return $this->isAsync() ? $value->toArray() : $value->join(', ');
                }

                return $default;
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
    public function resolveOptions(Request $request, Model $model): array
    {
        return $this->resolveRelatableQuery($request, $model)
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
    public function mapOption(Request $request, Model $model, Model $related): array
    {
        return [
            'value' => $related->getKey(),
            'formattedValue' => $this->resolveDisplay($request, $related),
        ];
    }

    /**
     * Get the route parameter name.
     */
    public function getParameterName(): string
    {
        return 'rootField';
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        if ($this->isAsync()) {
            $router->get('/', RelationFieldController::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $models = $this->getValue($request, $model);

        return array_merge(parent::toInput($request, $model), [
            'async' => $this->isAsync(),
            'nullable' => $this->isNullable(),
            'options' => $this->isAsync() ? [] : $this->resolveOptions($request, $model),
            'url' => $this->isAsync() ? $this->replaceRoutePlaceholders($request->route()) : null,
            'selection' => $models->map(function (Model $related) use ($request, $model): array {
                return $this->mapOption($request, $model, $related);
            })->toArray(),
        ]);
    }
}
