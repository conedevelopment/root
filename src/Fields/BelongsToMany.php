<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class BelongsToMany extends BelongsTo
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'BelongsToMany';

    /**
     * The pivot fields resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * The resolved components.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Set the async attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable(Request $request): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefault(Request $request, Model $model): mixed
    {
        if (is_null($this->defaultResolver)) {
            $this->defaultResolver = function (Request $request, Model $model, mixed $value): array {
                return $value->mapWithKeys(function (Model $related) use ($request, $model): array {
                    return [
                        $related->getKey() => $this->mapPivotValues($request, $model, $related),
                    ];
                })->toArray();
            };
        }

        return parent::resolveDefault($request, $model);
    }

    /**
     * Map the pivot values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    protected function mapPivotValues(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        return $this->resolvePivotFields($request)
                    ->available($request, $model, $related)
                    ->mapWithKeys(static function (Field $field) use ($request, $related, $relation): array {
                        return [
                            $field->name => $field->resolveDefault(
                                $request, $related->getRelation($relation->getPivotAccessor())
                            ),
                        ];
                    })
                    ->toArray();
    }

    /**
     * {@inheritdoc}
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

            $this->resolved['pivot_fields'] = $fields->each->mergeAuthorizationResolver(function (Request $request, ...$parameters): bool {
                return $this->authorized($request, ...$parameters);
            });
        }

        return $this->resolved['pivot_fields'];
    }

    /**
     * {@inheritdoc}
     */
    public function mapOption(Request $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        return array_merge(parent::mapOption($request, $model, $related), [
            'pivot_fields' => $this->resolvePivotFields($request)
                                    ->available($request, $model, $related)
                                    ->mapToForm($request, $relation->newPivot())
                                    ->toArray(),
        ]);
    }

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
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $models = $this->getDefaultValue($request, $model);

        $relation = $this->getRelation($model);

        return array_merge(parent::toInput($request, $model), [
            'async' => $this->async,
            'formatted_value' => $models->mapWithKeys(function (Model $related) use ($request): mixed {
                return [$related->getKey() => $this->resolveDisplay($request, $related)];
            }),
            'multiple' => true,
            'pivot_fields' => $models->mapWithKeys(function (Model $related) use ($request, $model, $relation): array {
                return [
                    $related->getKey() => $this->resolvePivotFields($request)
                                                ->available($request, $model, $related)
                                                ->mapToForm($request, $related->getRelation($relation->getPivotAccessor()))
                                                ->toArray(),
                ];
            }),
        ]);
    }

    /**
     * Get the validation representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toValidate(Request $request, Model $model): array
    {
        $pivotRules = $this->resolvePivotFields($request)
                            ->available($request, $model)
                            ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($pivotRules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [$this->name.'.*.'.$key => $rules];
                    })
                    ->toArray(),
        );
    }
}
