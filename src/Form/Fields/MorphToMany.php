<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Relations\MorphToMany as EloquentRelation;

class MorphToMany extends BelongsToMany
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
