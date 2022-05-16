<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;

class BelongsTo extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $related = $this->resolveQuery($request, $model)->find($value);

        $this->getRelation($model)->associate($related);
    }
}
