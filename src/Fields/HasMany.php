<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentRelation;
use Illuminate\Http\Request;

class HasMany extends HasOneOrMany
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
