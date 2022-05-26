<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class BelongsToMany extends BelongsTo
{
    use ResolvesFields;

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
    public function isSortable(RootRequest $request): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveDefault(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->defaultResolver)) {
            $this->defaultResolver = function (RootRequest $request, Model $model, mixed $value): array {
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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    protected function mapPivotValues(RootRequest $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        return $this->resolveFields($request)
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
    public function persist(RootRequest $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $value = $this->getValueForHydrate($request, $model);

            $this->hydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $results = $this->resolveQuery($request, $model)
                        ->findMany(array_keys($value))
                        ->each(static function (Model $related) use ($relation, $value): void {
                            $related->setRelation(
                                $relation->getPivotAccessor(),
                                $relation->newPivot($value[$related->getKey()])
                            );
                        });

        $model->setRelation($relation->getRelationName(), $results);
    }

    /**
     * {@inheritdoc}
     */
    public function mapOption(RootRequest $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        return array_merge(parent::mapOption($request, $model, $related), [
            'fields' => $this->resolveFields($request)
                            ->available($request, $model, $related)
                            ->mapToForm($request, $relation->newPivot())
                            ->toArray(),
        ]);
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $models = $this->getDefaultValue($request, $model);

        $relation = $this->getRelation($model);

        return array_merge(parent::toInput($request, $model), [
            'async' => $this->async,
            'fields' => $models->mapWithKeys(function (Model $related) use ($request, $model, $relation): array {
                return [
                    $related->getKey() => $this->resolveFields($request)
                                                ->available($request, $model, $related)
                                                ->mapToForm($request, $related->getRelation($relation->getPivotAccessor()))
                                                ->toArray(),
                ];
            }),
            'formatted_value' => $models->mapWithKeys(function (Model $related) use ($request): mixed {
                return [$related->getKey() => $this->resolveDisplay($request, $related)];
            }),
            'multiple' => true,
        ]);
    }

    /**
     * Get the validation representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toValidate(RootRequest $request, Model $model): array
    {
        $pivotRules = $this->resolveFields($request)
                            ->available($request, $model)
                            ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($pivotRules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [sprintf('%s.*.%s', $this->name, $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
