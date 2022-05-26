<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\HasManyController;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;

class HasMany extends HasOne
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
        $model->saved(function (Model $model) use ($request): void {
            $relation = $this->getRelation($model);

            $value = $this->getValueForHydrate($request, $model);

            $this->hydrate($request, $model, $value);

            $relation->saveMany(
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

        $results = $this->resolveQuery($request, $model)->findMany((array) $value);

        $model->setRelation($relation->getRelationName(), $results);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        parent::routes($router);

        $router->get('{rootResource}', [HasManyController::class, 'index']);
        $router->post('{rootResource}', [HasManyController::class, 'store']);
        $router->get('{rootResource}/create', [HasManyController::class, 'create']);
        $router->get('{rootResource}/{related}', [HasManyController::class, 'show']);
        $router->get('{rootResource}/{related}/edit', [HasManyController::class, 'edit']);
        $router->patch('{rootResource}/{related}', [HasManyController::class, 'update']);
        $router->delete('{rootResource}/{related}', [HasManyController::class, 'destroy']);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'multiple' => true,
            'related_name' => $this->getRelatedName(),
            'url' => URL::to(sprintf('%s/%s', $this->getUri(), $model->getKey())),
        ]);
    }
}
