<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\EventFactory;
use Cone\Root\Interfaces\Models\Event as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Event extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'action',
        'payload',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_events';

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
    protected static function newFactory(): Factory
    {
        return EventFactory::new();
    }

    /**
     * Get the event target.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::getProxiedClass());
    }

    /**
     * Get the event target.
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
