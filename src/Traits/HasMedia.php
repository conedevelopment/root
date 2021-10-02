<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasMedia
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasMedia(): void
    {
        static::deleting(static function (self $model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model)) || $model->forceDeleting) {
                $model->media()->detach();
            }
        });
    }

    /**
     * Get the media for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Medium::class, 'mediable', 'root_mediables');
    }
}
