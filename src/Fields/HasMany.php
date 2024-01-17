<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentRelation;

/**
 * @extends \Cone\Root\Fields\HasOneOrMany<\Illuminate\Database\Eloquent\Relations\HasMany>
 */
class HasMany extends HasOneOrMany
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null, Closure|string|null $relation = null)
    {
        parent::__construct($label, $modelAttribute, $relation);

        $this->setAttribute('multiple', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }
}
