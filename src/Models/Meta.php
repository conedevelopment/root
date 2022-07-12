<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\MetaFactory;
use Cone\Root\Interfaces\Models\Meta as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends Model implements Contract
{
    use InteractsWithProxy;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_metas';

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return MetaFactory::new();
    }

    /**
     * Get the metable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function metable(): MorphTo
    {
        return $this->morphTo();
    }
}
