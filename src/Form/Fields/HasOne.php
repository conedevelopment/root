<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Relations\HasOne as EloquentRelation;

class HasOne extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
