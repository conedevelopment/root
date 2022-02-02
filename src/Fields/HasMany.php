<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HasMany extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $models = $relation->getRelated()->newQuery()->findMany((array) $value);

        $model->saved(static function () use ($relation, $models): void {
            $relation->saveMany($models);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
        ]);
    }
}
