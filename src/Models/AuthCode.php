<?php

declare(strict_types=1);

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\AuthCodeFactory;
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_auth_codes';

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct(
            array_merge(['code' => static::generate()], $attributes)
        );
    }

    /**
     * Generate a new code.
     */
    public static function generate(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (preg_match('/(\d)\1{2,}/', $code) === 1);

        return $code;
    }

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
    protected static function newFactory(): AuthCodeFactory
    {
        return AuthCodeFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array{'code':'int', 'expires_at':'datetime'}
     */
    protected function casts(): array
    {
        return [
            'code' => 'int',
            'expires_at' => 'datetime',
        ];
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
