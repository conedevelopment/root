<?php

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
}
