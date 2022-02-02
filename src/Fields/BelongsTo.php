<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BelongsTo extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($value): void {
            $this->getRelation($model)->associate($value);
        });
    }
}
