<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\ResourceRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class Relation extends Field
{
    use ResourceRoutable;

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
    protected bool $async = false;

    /**
     * The Vue compoent.
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

        $this->display('id');
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
     * Set the display key name.
     *
     * @param  string  $value
     * @return $this
     */
    public function display(string|Closure $value): static
    {
        if (is_string($value)) {
            $value = static function (Request $request, Model $model) use ($value) {
                return $model->getAttribute($value);
            };
        }

        $this->displayResolver = $value;

        return $this;
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
     * Format the value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatter)) {
            $default = parent::resolveDefault($request, $model);

            $this->formatResolver = function () use ($request, $default): mixed {
                if ($default instanceof Model) {
                    return call_user_func_array($this->displayResolver, [$request, $default]);
                } elseif ($default instanceof Collection) {
                    return $default->map(function (Model $model) use ($request): mixed {
                        return call_user_func_array($this->displayResolver, [$request, $model]);
                    })->join(', ');
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
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the related model's eloquent query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function resolveQuery(Request $request, Model $model): Builder
    {
        $relation = call_user_func([$model, $this->relation]);

        $query = $relation->getModel()->newQuery();

        if (! is_null($this->queryResolver)) {
            call_user_func_array($this->queryResolver, [$request, $query]);
        }

        return $query;
    }

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return $this->resolveQuery($request, $model)
                    ->get()
                    ->mapWithKeys(function (Model $model) use ($request): array {
                        return [$model->getKey() => call_user_func_array($this->displayResolver, [$request, $model])];
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
            'async' => $this->async,
            'nullable' => $this->nullable,
            'options' => $this->async ? [] : $this->resolveOptions($request, $model),
            'url' => $this->async ? call_user_func($this->urlResolver) : null,
        ]);
    }

    /**
     * Regsiter the routes for the async component.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        if ($this->async) {
            $resource->routes(function () use ($uri): void {
                Route::get($uri, RelationController::class)->resolves($this->resolvedAs);
            });
        }
    }
}
