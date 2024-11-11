<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Translation;
use Cone\Root\Models\TranslationValue;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
     * Get the translation values for the model.
     */
    public function translationValues(): HasManyThrough
    {
        return $this->hasManyThrough(TranslationValue::getProxiedClass(), Translation::getProxiedClass(), 'translatable_id')
            ->where('root_translations.translatable_type', static::class)
            ->select(['*', 'root_translations.language as language']);
    }

    /**
     * Translate the value of the given key.
     */
    public function translate(string $key, ?string $language = null): mixed
    {
        $language ??= App::getLocale();

        $value = $this->translationValues->first(function (TranslationValue $value) use ($key, $language): bool {
            return $value->key === $key && $value->language === $language;
        });

        if (is_null($value)) {
            return $this->getAttribute($key);
        }

        return $value->mergeCasts(['key' => $this->getCasts()[$key] ?? 'string'])->value;
    }
}
