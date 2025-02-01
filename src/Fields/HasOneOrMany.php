<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\HasOneOrMany
 *
 * @extends \Cone\Root\Fields\Relation<TRelation>
 */
abstract class HasOneOrMany extends Relation
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
    public function persist(Request $request, Model $model, mixed $value): void
    {
        if ($this->isSubResource()) {
            parent::persist($request, $model, $value);
        } else {
            $model->saved(function (Model $model) use ($request, $value): void {
                $relation = $this->getRelation($model);

                $this->resolveHydrate($request, $model, $value);

                $models = $model->getRelation($this->getRelationName());

                $models = is_iterable($models) ? $models : Arr::wrap($models);

                foreach ($models as $related) {
                    $relation->save($related);
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $related = $this->resolveRelatableQuery($request, $model)
                    ->where(fn (Builder $query): Builder => $query->whereIn($this->getRelation($model)->getRelated()->getQualifiedKeyName(), (array) $value))
                    ->get();

                $model->setRelation($this->getRelationName(), is_array($value) ? $related : $related->first());
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }
}
