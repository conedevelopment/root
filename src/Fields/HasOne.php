<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne as EloquentRelation;

/**
 * @extends \Cone\Root\Fields\HasOneOrMany<\Illuminate\Database\Eloquent\Relations\HasOne>
 */
class HasOne extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
