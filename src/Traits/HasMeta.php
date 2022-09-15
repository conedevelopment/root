<?php

declare(strict_types = 1);

namespace Cone\Root\Traits;

use Cone\Root\Models\Meta;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMeta
{
    /**
     * Get the metas for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::getProxiedClass(), 'metable');
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key): mixed
    {
        if (str_starts_with($key, 'metas.')) {
            $casts = $this->getCasts();

            $meta = $this->metas->firstWhere('key', str_replace('metas.', '', $key)) ?: new Meta();

            if (array_key_exists($key, $casts)) {
                $meta->mergeCasts(['value' => $casts[$key]]);
            }

            return $meta->value;
        }

        return parent::getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value): mixed
    {
        if (str_starts_with($key, 'metas.')) {
            $casts = $this->getCasts();

            $meta = $this->metas->firstWhere('key', str_replace('metas.', '', $key))
                ?: $this->metas()->make(['key' => str_replace('metas.', '', $key)]);

            if (! $meta->exists) {
                $this->metas->push($meta);
            }

            if (array_key_exists($key, $casts)) {
                $meta->mergeCasts(['value' => $casts[$key]]);
            }

            $meta->value = $value;

            $this->saved(function () use ($meta): void {
                $this->metas()->save($meta);
            });

            return $this;
        }

        return parent::setAttribute($key, $value);
    }
}
