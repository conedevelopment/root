<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Http\Request;

class BelongsToMany extends Relation
{
    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        $relation = parent::getRelation($model);

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $value = $this->getValueForHydrate($request, $model);

            $this->resolveHydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $results = $this->resolveRelatableQuery($request, $model)->findMany((array) $value);

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
            'relatedName' => $this->getRelatedName(),
        ]);
    }
}
