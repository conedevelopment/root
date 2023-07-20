<?php

namespace Cone\Root\Relations;

use Cone\Root\Form\Fields\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;
use Illuminate\Database\Eloquent\Relations\MorphToMany as EloquentRelation;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MorphToMany extends BelongsToMany
{
    /**
     * The relatable field instance.
     */
    protected MorphTo $relatableField;

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }

    /**
     * Make a new relatable field.
     */
    public function newRelatableField(): MorphTo
    {
        return MorphTo::make($this->getRelatedName(), 'related', static function (Pivot $model): MorphToRelation {
            return $model->belongsTo(
                get_class($model->getRelation('related')),
                $model->getRelatedKey(),
                $model->getForeignKey(),
                'related'
            );
        });
    }
}
