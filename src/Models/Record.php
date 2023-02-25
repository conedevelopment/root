<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\RecordFactory;
use Cone\Root\Interfaces\Models\Record as Contract;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\RecordResource;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Record extends Model implements Contract, Resourceable
{
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
        'event',
        'properties',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'json',
    ];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_records';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RecordFactory::new();
    }

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Get the user for the record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::getProxiedClass());
    }

    /**
     * Get the target for the record.
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the resource representation of the model.
     */
    public static function toResource(): RecordResource
    {
        return new RecordResource(static::class);
    }
}
