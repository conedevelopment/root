<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentRelation;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\BelongsTo
 * @extends  \Cone\Root\Fields\Relation<TRelation>
 */
class BelongsTo extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $this->getRelation($model)->associate($value);
    }
}
