<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\TranslationFactory;
use Cone\Root\Interfaces\Models\Translation as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'values' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'locale',
        'values',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_translations';

    /**
     * The translatable model's locale.
     */
    protected static string $translatableLocale = 'en';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TranslationFactory
    {
        return TranslationFactory::new();
    }

    /**
     * Set the translatable model's locale.
     */
    public static function setTranslatableLocale(string $locale): void
    {
        static::$translatableLocale = $locale;
    }

    /**
     * Get the translatable model's locale.
     */
    public static function getTranslatableLocale(): string
    {
        return static::$translatableLocale;
    }

    /**
     * Get the translatable model for the translation.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
