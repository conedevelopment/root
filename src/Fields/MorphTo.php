<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentRelation;

class MorphTo extends BelongsTo
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
