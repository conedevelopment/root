<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Relations\MorphMany as EloquentRelation;

class MorphMany extends MorphOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
