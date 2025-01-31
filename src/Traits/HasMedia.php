<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Attachment;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasMedia
{
    /**
     * Boot the trait.
     */
    protected static function bootHasMedia(): void
    {
        static::deleting(static function (self $model): void {
            if ($model->forceDeleting || ! in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model->media()->detach();
            }
        });
    }

    /**
     * Get the media for the model.
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Medium::getProxiedClass(), 'mediable', 'root_mediables')
            ->as('attachment')
            ->using(Attachment::class)
            ->withPivot(['meta', 'collection'])
            ->withTimestamps();
    }
}
