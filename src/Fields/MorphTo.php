<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentRelation;

/**
 * @extends \Cone\Root\Fields\BelongsTo<\Illuminate\Database\Eloquent\Relations\MorphTo>
 */
class MorphTo extends BelongsTo
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
