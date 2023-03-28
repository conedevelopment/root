<?php

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
}