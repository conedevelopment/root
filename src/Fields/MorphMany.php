<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany as EloquentRelation;

/**
 * @extends \Cone\Root\Fields\MorphOneOrMany<\Illuminate\Database\Eloquent\Relations\MorphMany>
 */
class MorphMany extends MorphOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
