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
    public function translate(string $key, ?string $locale = null): mixed
    {
        $locale ??= App::getLocale();

        return match ($locale) {
            (Translation::proxy())::getTranslatableLocale() => $this->getAttribute($key),
            default => $this->translations->firstWhere('locale', $locale)?->values[$key] ?? null,
        };
    }
}
