<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentRelation;

class HasMany extends HasOneOrMany
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->setAttribute('multiple', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }
}
