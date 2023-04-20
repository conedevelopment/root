<?php

namespace Cone\Root\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentRelation;

class HasMany extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
