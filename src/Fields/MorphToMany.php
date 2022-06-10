<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;
use Illuminate\Database\Eloquent\Relations\MorphToMany as EloquentRelation;

/**
 * @extends \Cone\Root\Fields\BelongsToMany<\Illuminate\Database\Eloquent\Relations\MorphToMany>
 */
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
            ->display(function (RootRequest $request, Model $related): mixed {
                return $this->resolveDisplay($request, $related);
            }),
        ];
    }
}
