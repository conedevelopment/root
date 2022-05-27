<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\BelongsToManyController;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Resources\ModelResource;
use Cone\Root\Http\Resources\RelatedResource;
use Cone\Root\Traits\AsSubResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;

class BelongsToMany extends BelongsTo
{
    use AsSubResource;

    /**
     * Set the async attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        return $this;
    }

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
            $value = $this->getValueForHydrate($request, $model);

            $this->hydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
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
     * Map the related model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return \Cone\Root\Http\Resources\ModelResource
     */
    public function mapItem(ResourceRequest $request, Model $model, Model $related): ModelResource
    {
        $relation = $this->getRelation($model);

        $pivot = $related->relationLoaded($relation->getPivotAccessor())
                    ? $related->getRelation($relation->getPivotAccessor())
                    : $relation->newPivot();

        // $pivot->setAttribute($pivot->getRelatedKey(), $related->getKey());

        $pivot->setRelation('related', $related);

        return new RelatedResource($pivot);
    }

    /**
     * Define the fields for the object.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return [
            //
        ];
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
        });
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        parent::routes($router);

        $router->get('{rootResource}', [BelongsToManyController::class, 'index']);
        // $router->post('{rootResource}', [HasManyController::class, 'store']);
        // $router->get('{rootResource}/create', [HasManyController::class, 'create']);
        // $router->get('{rootResource}/{related}', [HasManyController::class, 'show']);
        // $router->get('{rootResource}/{related}/edit', [HasManyController::class, 'edit']);
        // $router->patch('{rootResource}/{related}', [HasManyController::class, 'update']);
        // $router->delete('{rootResource}/{related}', [HasManyController::class, 'destroy']);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'async' => $this->async,
            'multiple' => true,
            'related_name' => $this->getRelatedName(),
            'url' => URL::to(sprintf('%s/%s', $this->getUri(), $model->getKey())),
        ]);
    }
}
