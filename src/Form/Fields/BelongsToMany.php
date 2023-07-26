<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Http\Request;

class BelongsToMany extends Relation
{
    /**
     * Create a new relation field instance.
     */
    public function __construct(Form $form, string $label, string $name = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $name, $relation);

        $this->setAttribute('multiple', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(): EloquentRelation
    {
        $relation = parent::getRelation();

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveModel()->saved(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);

            $this->getRelation()->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $results = $this->resolveRelatableQuery($request, $model)->findMany((array) $value);

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'relatedName' => $this->getRelatedName(),
        ]);
    }
}
