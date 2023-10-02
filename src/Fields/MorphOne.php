<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Relations\MorphOne as EloquentRelation;

class MorphOne extends MorphOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
