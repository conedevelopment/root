<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class HasOneOrMany extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveModel()->saved(function (Model $model) use ($request, $value): void {
            $relation = $this->getRelation();

            $this->resolveHydrate($request, $value);

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
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $related = $this->resolveRelatableQuery()->find($value);

                $model->setRelation($this->getRelationName(), $related);
            };
        }

        parent::resolveHydrate($request, $value);
    }
}
