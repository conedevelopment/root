<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Translatable
{
    /**
     * Get the translations for the model.
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::getProxiedClass(), 'translatable');
    }
}
