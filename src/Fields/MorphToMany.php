<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;
use Illuminate\Database\Eloquent\Relations\MorphToMany as EloquentRelation;
use Illuminate\Http\Request;

class MorphToMany extends BelongsToMany
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
    public function fields(Request $request): array
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
            ->withRelatableQuery(function (Request $request, Builder $query, MorphPivot $model): Builder {
                return $this->resolveRelatableQuery($request, $model->pivotParent);
            })
            ->display(function (Model $model): mixed {
                return $this->resolveDisplay($model);
            }),
        ];
    }
}
