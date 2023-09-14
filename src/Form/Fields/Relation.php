<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Fields\Options\OptGroup;
use Cone\Root\Form\Fields\Options\RelationOption;
use Cone\Root\Form\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Relation extends Field
{
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
    public function __construct(Form $form, string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $modelAttribute);

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
    public function getRelation(): EloquentRelation
    {
        if ($this->relation instanceof Closure) {
            return call_user_func_array($this->relation, [$this->getModel()]);
        }

        return call_user_func([$this->getModel(), $this->relation]);
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
            ? $this->getModelAttribute()
            : $this->relation;
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
    public function getValue(): mixed
    {
        $model = $this->getModel();

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
    public function resolveRelatableQuery(Request $request): Builder
    {
        $model = $this->getModel();

        $query = $this->getRelation()->getRelated()->newQuery();

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
    public function resolveOptions(Request $request): array
    {
        $value = $this->resolveValue($request);

        $mapCallback = function (Model $related) use ($value): RelationOption {
            return $this->toOption($related)
                ->selected($value instanceof Model ? $value->is($related) : $value->contains($related));
        };

        return $this->resolveRelatableQuery($request)
            ->get()
            ->when(! is_null($this->groupResolver), function (Collection $collection) use ($mapCallback): Collection {
                return $collection->groupBy($this->groupResolver)
                    ->map(function ($group, $key) use ($mapCallback): OptGroup {
                        return new OptGroup($key, $group->map($mapCallback)->all());
                    });
            }, function (Collection $collection) use ($mapCallback): Collection {
                return $collection->map($mapCallback);
            })
            ->toArray();
    }

    /**
     * Make a new option instance.
     */
    public function newOption(Model $related, string $label): RelationOption
    {
        return new RelationOption($related, $label);
    }

    /**
     * Build the API URI.
     */
    protected function buildApiUri(): ?string
    {
        if (is_null($this->apiUri)) {
            return $this->apiUri;
        }

        return sprintf('%s?%s', $this->apiUri, http_build_query([
            'model' => $this->getModel()->getKey(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'async' => $this->isAsync(),
                    'nullable' => $this->isNullable(),
                    'options' => $this->isAsync() ? [] : $this->resolveOptions($request),
                    'url' => $this->isAsync() ? $this->buildApiUri() : null,
                ];
            })
        );
    }

    /**
     * Convert the related model to an option.
     */
    public function toOption(Model $related): RelationOption
    {
        return $this->newOption($related, $this->resolveDisplay($related));
    }
}
