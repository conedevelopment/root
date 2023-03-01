<?php

namespace Cone\Root\Traits;

use Cone\Root\Support\Slug;

trait Sluggable
{
    /**
     * Boot the trait.
     */
    protected static function bootSluggable(): void
    {
        static::saving(static function (self $model): void {
            $slug = $model->toSlug();

            $value = $slug->generate();

            $model->setAttribute($slug->to, $value);
        });
    }

    /**
     * Get the slug representation of the model.
     */
    public function toSlug(): Slug
    {
        return (new Slug($this))->unique();
    }
}
