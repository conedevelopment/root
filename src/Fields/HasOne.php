<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HasOne extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $related = $relation->getRelated()->newQuery()->find($value);

        $model->saved(static function () use ($relation, $related): void {
            $relation->save($related);
        });
    }
}
