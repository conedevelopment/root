<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Relation extends Field
{
    /**
     * The relation name on the model.
     *
     * @var string
     */
    protected string $relation;

    /**
     * Indicates if the field should be nullable.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * Indicates if the options should be lazily populated.
     *
     * @var bool
     */
    protected bool $lazy = false;

    /**
     * The display key name.
     *
     * @var string
     */
    protected string $displayKeyName = 'id';

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'FormSelect';

    /**
     * The query resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $queryResolver = null;

    /**
     * Create a new relation field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @param  string|null  $relation
     * @return void
     */
    public function __construct(string $label, ?string $name = null, ?string $relation = null)
    {
        parent::__construct($label, $name);

        $this->relation = $relation ?: Str::camel($label);
    }

    /**
     * Set the nullable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Set the display key name.
     *
     * @param  string  $value
     * @return $this
     */
    public function display(string $value): self
    {
        $this->displayKeyName = $value;

        return $this;
    }

    /**
     * Set the lazy attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function lazy(bool $value = true): self
    {
        $this->lazy = $value;

        return $this;
    }

    /**
     * Format the value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatter)) {
            $default = $this->resolveDefault($request, $model);

            $this->formatter = function () use ($default): mixed {
                if ($default instanceof Model) {
                    return $default->getAttribute($this->displayKeyName);
                } elseif ($default instanceof Collection) {
                    return $default->map->getAttribute($this->displayKeyName)->toArray();
                }

                return $default;
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Resolve the default value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveDefault(Request $request, Model $model): mixed
    {
        $default = parent::resolveDefault($request, $model);

        if ($default instanceof Model) {
            return $default->getKey();
        } elseif ($default instanceof Collection) {
            return $default->map->getKey()->toArray();
        }

        return $default;
    }

    /**
     * Set the query resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withQuery(Closure $callback): self
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function resolveOptions(Request $request, Model $model): array
    {
        if (! method_exists($model, $this->relation)) {
            return [];
        }

        $query = call_user_func([$model, $this->relation])->getModel()->newQuery();

        if (! is_null($this->queryResolver)) {
            call_user_func_array($this->queryResolver, [$query, $request, $model]);
        }

        return $query->get()
                    ->mapWithKeys(function (Model $model): array {
                        return [$model->getKey() => $model->getAttribute($this->displayKeyName)];
                    })
                    ->toArray();
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'lazy' => $this->lazy,
            'nullable' => $this->nullable,
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
