<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\UserFactory;
use Cone\Root\Interfaces\Models\User as Contract;
use Cone\Root\Resources\Resource;
use Cone\Root\Resources\UserResource;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Cone\Root\Traits\InteractsWithResources;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class User extends Authenticatable implements Contract, MustVerifyEmail
{
    use Filterable;
    use HasFactory;
    use InteractsWithProxy;
    use InteractsWithResources;
    use Notifiable;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'email' => null,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(static function (self $user): void {
            $user->password = $user->password ?: Hash::make(Str::random(10));
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory|null
     */
    protected static function newFactory(): ?Factory
    {
        return get_called_class() === self::class ? UserFactory::new() : null;
    }

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
     * Get the uploads for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(Medium::getProxiedClass());
    }

    /**
     * Get the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return URL::asset('vendor/root/img/avatar-placeholder.svg');
    }

    /**
     * Scope the query only to the given search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where(static function (Builder $query) use ($value): Builder {
            return $query->where($query->qualifyColumn('name'), 'like', "%{$value}%")
                        ->orWhere($query->qualifyColumn('email'), 'like', "%{$value}%");
        });
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new UserResource(static::class);
    }
}
