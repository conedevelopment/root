<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get the translations for the model.
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::getProxiedClass(), 'translatable');
    }

    /**
     * Translate the value of the given key.
     */
    public function translate(string $key, ?string $language = null): mixed
    {
        $language ??= App::getLocale();

        $translation = $this->translations->firstWhere('language', $language);

        return $translation?->values[$key] ?? null;
    }
}
