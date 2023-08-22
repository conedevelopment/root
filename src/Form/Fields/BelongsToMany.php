<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class BelongsToMany extends Relation
{
    use ResolvesFields;

    /**
     * The pivot fields resolver callback.
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * Create a new relation field instance.
     */
    public function __construct(Form $form, string $label, string $key = null, Closure|string $relation = null)
    {
        parent::__construct($form, $label, $key, $relation);

        $this->setAttribute('multiple', true);

        $this->fields = new PivotFields($form);
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
     * Set the pivot field resolver.
     */
    public function withPivotFields(Closure $callback): static
    {
        // $this->withFields(function (Fields $fields) use ($callback): Fields {
        //     return call_user_func_array($callback, [
        //         $fields,
        //         $this->newOption($this->getRelation()->getRelated(), '')
        //     ]);
        // });

        $this->pivotFieldsResolver = $callback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(Model $related): RelationOption
    {
        $relation = $this->getRelation();

        if (! $related->relationLoaded($relation->getPivotAccessor())) {
            $related->setRelation($relation->getPivotAccessor(), $relation->newPivot());
        }

        return parent::toOption($related);
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

                $results = $this->resolveRelatableQuery($request, $model)
                    ->findMany((array) array_keys($value))
                    ->each(static function (Model $related) use ($relation, $value): void {
                        $related->setRelation(
                            $relation->getPivotAccessor(),
                            $relation->newPivot($value[$related->getKey()])
                        );
                    });

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

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(Router $router): void
    {
        parent::registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->fields->registerRoutes($router);
        });
    }
}
