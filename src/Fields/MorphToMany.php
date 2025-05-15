<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;
use Illuminate\Database\Eloquent\Relations\MorphToMany as EloquentRelation;
use Illuminate\Http\Request;

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
    public function fields(Request $request): array
    {
        return [
            MorphTo::make($this->getRelatedName(), 'related', static fn (MorphPivot $model): MorphToRelation => $model->morphTo(
                'related',
                $model->getMorphType(),
                $model->getRelatedKey(),
                $model->getForeignKey(),
            )->withDefault())
                ->withRelatableQuery(
                    fn (Request $request, Builder $query, MorphPivot $model): Builder => $this->resolveRelatableQuery($request, $model->pivotParent)
                        ->unless($this->allowDuplicateRelations, fn (Builder $query): Builder => $query->whereNotIn(
                            $query->getModel()->getQualifiedKeyName(),
                            $this->getRelation($model->pivotParent)->select($query->getModel()->getQualifiedKeyName())
                        )))
                ->hydrate(function (Request $request, MorphPivot $model, mixed $value): void {
                    $model->setAttribute(
                        $this->getRelation($model->pivotParent)->getRelatedPivotKeyName(),
                        $value
                    );
                })->display(fn (Model $model): ?string => $this->resolveDisplay($model)),
        ];
    }
}
