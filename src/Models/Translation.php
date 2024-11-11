<?php

namespace Cone\Root\Models;

use App\Mail\Contact;
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
        'language',
        'values',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_translations';

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
    protected static function newFactory(): TranslationFactory
    {
        return TranslationFactory::new();
    }

    /**
     * Get the translatable model for the translation.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
