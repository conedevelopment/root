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
        $related = $this->resolveQuery($request, $model)->find($value);

        $this->getRelation($model)->associate($related);
    }
}
