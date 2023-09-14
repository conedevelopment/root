<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentRelation;

class HasMany extends HasOneOrMany
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $modelAttribute, $relation);

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
