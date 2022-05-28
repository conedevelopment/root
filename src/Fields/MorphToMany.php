<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;

class MorphToMany extends BelongsToMany
{
        /**
     * Define the fields for the object.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return [
            MorphTo::make($this->getRelatedName(), 'related', static function (MorphPivot $model): MorphToRelation {
                return $model->morphTo(
                    'related',
                    $model->getMorphType(),
                    $model->getRelatedKey(),
                    $model->getForeignKey(),
                );
            })
            ->async($this->async)
            ->withQuery(function (RootRequest $request, Model $model): Builder {
                return $this->resolveQuery($request, $model);
            })
            ->display(function (RootRequest $request, Model $related) {
                return $this->resolveDisplay($request, $related);
            }),
        ];
    }
}
