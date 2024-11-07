<?php

namespace Cone\Root\Models;

use App\Mail\Contact;
use Cone\Root\Database\Factories\TranslationValueFactory;
use Cone\Root\Interfaces\Models\TranslationValue as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationValue extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_translation_values';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contact::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TranslationValueFactory
    {
        return TranslationValueFactory::new();
    }

    /**
     * Get the translation for the translation value.
     */
    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::getProxiedClass());
    }
}
