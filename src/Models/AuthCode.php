<?php

namespace Cone\Root\Models;

use Cone\Root\Interfaces\Models\AuthCode as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

class AuthCode extends Model implements Contract
{
    use HasFactory;
    use InteractsWithProxy;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'code' => 'int',
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_auth_codes';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine whether the code is active.
     */
    public function active(): bool
    {
        return $this->expires_at->gt(Date::now());
    }

    /**
     * Determine whether the code is expired.
     */
    public function expired(): bool
    {
        return ! $this->active();
    }

    /**
     * Scope the query only to include the active codes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('expires_at'), '>', Date::now());
    }

    /**
     * Scope the query only to include the expired codes.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('expires_at'), '<=', Date::now());
    }
}
