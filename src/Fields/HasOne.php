<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HasOne extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $this->hydrate(
                $request, $model, $this->getValueForHydrate($request, $model)
            );

            $relation = $this->getRelation($model);

            $relation->save(
                $model->getRelation($relation->getRelationName())
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $result = $this->resolveQuery($request, $model)->find($value);

        $model->setRelation($relation->getRelationName(), $result);
    }
}
