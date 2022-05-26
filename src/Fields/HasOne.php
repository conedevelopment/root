<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\AsSubResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;

class HasOne extends Relation
{
    use AsSubResource;

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        parent::async($value);

        if ($this->asSubResource) {
            $this->component = 'SubResource';
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(RootRequest $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $this->hydrate(
                $request, $model, $this->getValueForHydrate($request, $model)
            );

            $relation = $this->getRelation($model);

            $relation->save(
                $model->getRelation($relation->getRelationName())
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $relation = $this->getRelation($model);

        $result = $this->resolveQuery($request, $model)->find($value);

        $model->setRelation($relation->getRelationName(), $result);
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        parent::registerRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
            $this->resolveActions($request)->registerRoutes($request, $router);
        });
    }
}
