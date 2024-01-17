<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\MorphOne
 *
 * @extends \Cone\Root\Fields\MorphOneOrMany<TRelation>
 */
class MorphOne extends MorphOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
