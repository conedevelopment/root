<?php

declare(strict_types=1);

namespace Cone\Root\Traits;

use Cone\Root\Models\Meta;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMetaData
{
    /**
     * Get the meta data for the model.
     */
    public function metaData(): MorphMany
    {
        return $this->morphMany(Meta::getProxiedClass(), 'metable');
    }

    /**
     * Get the meta value of the given key.
     */
    public function metaValue(string $key, mixed $default = null): mixed
    {
        return $this->metaData->firstWhere('key', $key)?->value ?: $default;
    }
}
