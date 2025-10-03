<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
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
            BelongsTo::make($this->getRelatedName(), 'related', static function (Model $model): BelongsToRelation {
                return $model->belongsTo(
                    $model::class,
                    $model->getRelatedKey(),
                    $model->getForeignKey()
                )->withDefault();
            })->withRelatableQuery(function (Request $request, Builder $query, Model $model): Builder {
                return $this->resolveRelatableQuery($request, $model->pivotParent)
                    ->unless($this->allowDuplicateRelations, function (Builder $query) use ($model): Builder {
                        return $query->whereNotIn(
                            $query->getModel()->getQualifiedKeyName(),
                            $this->getRelation($model->pivotParent)
                                ->select($query->getModel()->getQualifiedKeyName())
                                ->whereNot(
                                    $query->getModel()->getQualifiedKeyName(),
                                    $model->getAttribute($model->getRelatedKey())
                                )
                        );
                    });
            })->hydrate(function (Request $request, MorphPivot $model, mixed $value): void {
                $model->setAttribute(
                    $this->getRelation($model->pivotParent)->getRelatedPivotKeyName(),
                    $value
                );
            })->display(fn (Model $model): ?string => $this->resolveDisplay($model)),
        ];
    }
}
