<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
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
     * Indicates if the field should be nullable.
     */
    protected bool $nullable = false;

    /**
     * Indicates if the component is async.
     */
    protected bool $async = false;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.select';

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
    public function __construct(Form $form, string $label, string $name = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $name);

        $this->relation = $relation ?: $this->getKey();
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
    public function getRelation(): EloquentRelation
    {
        if ($this->relation instanceof Closure) {
            return call_user_func_array($this->relation, [$this->resolveModel()]);
        }

        return call_user_func([$this->resolveModel(), $this->relation]);
    }

    /**
     * Get the related model name.
     */
    public function getRelatedName(): string
    {
        return __(Str::of($this->getKey())->singular()->headline()->value());
    }

    /**
     * Get the relation name.
     */
    public function getRelationName(): string
    {
        return $this->relation instanceof Closure
            ? $$this->getKey()
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
     * Set the async attribute.
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        // $this->template = $value ? 'root::form.fields.dropdown' : 'root::form.fields.select';

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
    public function resolveValue(): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (Model $model, mixed $value): mixed {
                if ($value instanceof Model) {
                    return $value->getKey();
                } elseif ($value instanceof Collection) {
                    return $value->map->getKey()->toArray();
                }

                return $value;
            };
        }

        return parent::resolveValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): mixed
    {
        $model = $this->resolveModel();

        $name = $this->getRelationName();

        if ($this->relation instanceof Closure && ! $model->relationLoaded($name)) {
            $model->setRelation($name, call_user_func_array($this->relation, [$model])->getResults());
        }

        return $model->getAttribute($name);
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
    public function resolveRelatableQuery(): Builder
    {
        $model = $this->resolveModel();

        $query = $this->getRelation()->getRelated()->newQuery();

        foreach (static::$scopes[static::class] ?? [] as $scope) {
            $query = call_user_func_array($scope, [$query, $model]);
        }

        if (! is_null($this->queryResolver)) {
            $query = call_user_func_array($this->queryResolver, [$query, $model]);
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
    public function resolveOptions(): array
    {
        $value = $this->resolveValue();

        return $this->resolveRelatableQuery()
            ->get()
            ->when(! is_null($this->groupResolver), function (Collection $collection) use ($value): Collection {
                return $collection->groupBy($this->groupResolver)->map(function ($group, $key) use ($value): OptGroup {
                    $options = $group->map(function (Model $related) use ($value): Option {
                        return $this->newOption($related->getKey(), $this->resolveDisplay($related))
                            ->selected($value instanceof Model ? $value->is($related) : $value->contains($related));
                    });

                    return new OptGroup($key, $options->all());
                });
            }, function (Collection $collection) use ($value): Collection {
                return $collection->map(function (Model $related) use ($value): Option {
                    return $this->newOption($related->getKey(), $this->resolveDisplay($related))
                        ->selected($value instanceof Model ? $value->is($related) : $value->contains($related));
                });
            })
            ->toArray();
    }

    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): Option
    {
        return new Option($label, $value);
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
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'async' => $this->isAsync(),
            'nullable' => $this->isNullable(),
            'options' => $this->isAsync() ? [] : $this->resolveOptions(),
            'url' => $this->isAsync() ? $this->replaceRoutePlaceholders($request->route()) : null,
        ]);
    }
}
