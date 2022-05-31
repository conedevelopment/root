<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\HasManyController;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;

class HasMany extends HasOneOrMany
{
    /**
     * {@inheritdoc}
     */
    public function isSortable(RootRequest $request): bool
    {
        return false;
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

            $this->hydrate($request, $model, $value);

            $relation->saveMany(
                $model->getRelation($this->relation)
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $results = $this->resolveQuery($request, $model)->findMany((array) $value);

        $model->setRelation($this->relation, $results);
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

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
        ]);
    }
}
