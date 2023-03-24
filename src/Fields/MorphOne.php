<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;
use Illuminate\Http\Request;

class MorphOne extends MorphOneOrMany
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
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
        ]);
    }
}
