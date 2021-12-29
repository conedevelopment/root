<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HasOne extends Relation
{
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
        $relation = $this->getRelation($model);

        $related = $relation->getRelated()->newQuery()->find($value);

        $model->saved(static function () use ($relation, $related): void {
            $relation->save($related);
        });
    }
}
