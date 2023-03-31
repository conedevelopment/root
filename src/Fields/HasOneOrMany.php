<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class HasOneOrMany extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $relation = $this->getRelation($model);

            $value = $this->getValueForHydrate($request, $model);

            $this->resolveHydrate($request, $model, $value);

            $models = $model->getRelation($this->getRelationName());

            $models = is_iterable($models) ? $models : Arr::wrap($models);

            foreach ($models as $related) {
                $relation->save($related);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $related = $this->resolveQuery($request, $model)->find($value);

                $model->setRelation($this->getRelationName(), $related);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }
}
