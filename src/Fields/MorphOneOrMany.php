<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany as EloquentRelation;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
 *
 * @extends \Cone\Root\Fields\HasOneOrMany<TRelation>
 */
abstract class MorphOneOrMany extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
