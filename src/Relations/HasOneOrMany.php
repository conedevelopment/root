<?php

namespace Cone\Root\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentRelation;

abstract class HasOneOrMany extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
