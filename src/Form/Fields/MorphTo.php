<?php

namespace Cone\Root\Form\Fields;

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
