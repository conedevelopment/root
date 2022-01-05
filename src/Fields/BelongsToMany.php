<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class BelongsToMany extends BelongsTo
{
    /**
     * The pivot fields resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * The resolved store.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Register the field routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolvePivotFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * Hydrate the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return void
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saved(function (Model $model) use ($value): void {
            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * Set the pivot fields resolver.
     *
     * @param  array|\Closure  $fields
     * @return $this
     */
    public function withPivotFields(array|Closure $fields): static
    {
        if (is_array($fields)) {
            $fields = static function (Request $request, Fields $collection) use ($fields): Fields {
                return $collection->merge($fields);
            };
        }

        $this->pivotFieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve the pivot fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolvePivotFields(Request $request): Fields
    {
        if (! isset($this->resolved['pivot_fields'])) {
            $fields = Fields::make();

            if (! is_null($this->pivotFieldsResolver)) {
                $fields = call_user_func_array($this->pivotFieldsResolver, [$request, $fields]);
            }

            $this->resolved['pivot_fields'] = $fields;
        }

        return $this->resolved['pivot_fields'];
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
            'fields' => $this->resolvePivotFields($request)->available($request)->toArray(),
            'multiple' => true,
        ]);
    }
}
