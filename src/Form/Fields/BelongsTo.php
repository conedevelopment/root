<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentRelation;
use Illuminate\Http\Request;

class BelongsTo extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        return parent::getRelation();
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $this->getRelation()->associate($value);
            };
        }

        parent::resolveHydrate($request, $value);
    }
}
