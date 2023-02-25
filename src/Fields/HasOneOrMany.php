<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\HasOneOrManyController;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\AsSubResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentRelation;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

abstract class HasOneOrMany extends Relation
{
    use AsSubResource;

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
    public function visible(RootRequest $request): bool
    {
        if ($this->asSubResource && $request instanceof CreateRequest) {
            return false;
        }

        return parent::visible($request);
    }

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
        if ($this->asSubResource) {
            return;
        }

        $model->saved(function (Model $model) use ($request): void {
            $relation = $this->getRelation($model);

            $value = $this->getValueForHydrate($request, $model);

            $this->resolveHydrate($request, $model, $value);

            $models = $model->getRelation($this->name);

            $models = is_iterable($models) ? $models : Arr::wrap($models);

            foreach ($models as $related) {
                $relation->save($related);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (RootRequest $request, Model $model, mixed $value): void {
                $related = $this->resolveQuery($request, $model)->find($value);

                $model->setRelation($this->name, $related);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Fields\Field  $field
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

        if ($this->asSubResource) {
            $router->prefix($this->getKey()."/{{$this->getRouteKeyName()}}")->group(function (Router $router) use ($request): void {
                $this->resolveFields($request)->registerRoutes($request, $router);
                $this->resolveActions($request)->registerRoutes($request, $router);
            });

            $this->registerRouterConstrains($request, $router);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        if ($this->asSubResource) {
            $router->get('/', [HasOneOrManyController::class, 'index']);
            $router->post('/', [HasOneOrManyController::class, 'store']);
            $router->get('/create', [HasOneOrManyController::class, 'create']);
            $router->get("/{{$this->getRouteKeyName()}}", [HasOneOrManyController::class, 'show']);
            $router->get("/{{$this->getRouteKeyName()}}/edit", [HasOneOrManyController::class, 'edit']);
            $router->patch("/{{$this->getRouteKeyName()}}", [HasOneOrManyController::class, 'update']);
            $router->delete("/{{$this->getRouteKeyName()}}", [HasOneOrManyController::class, 'destroy']);
        } else {
            parent::routes($router);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $input = parent::toInput($request, $model);

        return array_merge($input, [
            'related_name' => $this->getRelatedName(),
            'options' => $this->asSubResource ? [] : $input['options'],
            'url' => $this->resolveUri($request),
        ]);
    }
}
