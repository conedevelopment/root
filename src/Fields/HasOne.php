<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne as HasOneRelation;
use Illuminate\Routing\Router;

class HasOne extends HasOneOrMany
{
    /**
     * Get the relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getRelation(Model $model): HasOneRelation
    {
        return parent::getRelation($model);
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
            $this->hydrate(
                $request, $model, $this->getValueForHydrate($request, $model)
            );

            $relation = $this->getRelation($model);

            $relation->save(
                $model->getRelation($this->relation)
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $result = $this->resolveQuery($request, $model)->find($value);

        $model->setRelation($this->relation, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        if ($this->asSubResource) {
            $router->get('{rootResource}', [HasManyController::class, 'index']);
            $router->post('{rootResource}', [HasManyController::class, 'store']);
            $router->get('{rootResource}/create', [HasManyController::class, 'create']);
            $router->get('{rootResource}/{related}', [HasManyController::class, 'show']);
            $router->get('{rootResource}/{related}/edit', [HasManyController::class, 'edit']);
            $router->patch('{rootResource}/{related}', [HasManyController::class, 'update']);
            $router->delete('{rootResource}/{related}', [HasManyController::class, 'destroy']);
        } else {
            parent::routes($router);
        }
    }
}
