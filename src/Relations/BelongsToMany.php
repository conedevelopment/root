<?php

namespace Cone\Root\Relations;

use Closure;
use Cone\Root\Fields\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentRelation;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;

class BelongsToMany extends Relation
{
    /**
     * The relatable field instance.
     */
    protected BelongsTo $relatableField;

    /**
     * Create a new relation instance.
     */
    public function __construct(string $label, string $relation)
    {
        parent::__construct($label, $relation);

        $this->relatableField = $this->newRelatableField();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        $relation = parent::getRelation($model);

        return $relation->withPivot($relation->newPivot()->getKeyName());
    }

    /**
     * Make a new item instance.
     */
    public function newItem(Model $model, Model $related): PivotItem
    {
        $relation = $this->getRelation($model);

        $pivot = $related->relationLoaded($relation->getPivotAccessor())
                ? $related->getRelation($relation->getPivotAccessor())
                : $relation->newPivot();

        $pivot->setRelation('parent', $model)
            ->setAttribute('rootRelation', $this->relation)
            ->setRelation('related', $related)
            ->setAttribute($pivot->getKeyName(), $pivot->getKey())
            ->setAttribute($relation->getForeignPivotKeyName(), $model->getKey());

        return (new PivotItem($pivot))->url(function (Request $request) use ($pivot): string {
            return $pivot->exists
                ? sprintf('%s/%s', $this->replaceRoutePlaceholders($request->route()), $pivot->getRouteKey())
                : $this->replaceRoutePlaceholders($request->route());
        });
    }

    /**
     * Make a new relatable field.
     */
    public function newRelatableField(): BelongsTo
    {
        return BelongsTo::make($this->getRelatedName(), 'related', static function (Pivot $model): BelongsToRelation {
            return $model->belongsTo(
                get_class($model->getRelation('related')),
                $model->getRelatedKey(),
                $model->getForeignKey(),
                'related'
            );
        });
    }

    /**
     * Customize the relatable field.
     */
    public function withRelatableField(Closure $callback): static
    {
        $this->relatableField = call_user_func_array($callback, [$this->relatableField]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return array_merge(parent::fields($request), [
            $this->relatableField,
        ]);
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        $relation = $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()));

        return $relation->wherePivot($relation->newPivot()->getQualifiedKeyName(), $id)->firstOrFail();
    }
}
