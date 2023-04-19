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
}
