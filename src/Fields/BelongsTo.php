<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\BelongsTo
 *
 * @extends \Cone\Root\Fields\Relation<TRelation>
 */
class BelongsTo extends Relation
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
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, mixed $value): void {
                $this->getRelation($model)->associate($value);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function asSubResource(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubResource(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        //
    }
}
