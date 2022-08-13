<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\UserFactory;
use Cone\Root\Interfaces\Models\User as Contract;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable implements Contract, Resourceable
{
    use Filterable;
    use HasFactory;
    use InteractsWithProxy;
    use Notifiable;
    use SoftDeletes;

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
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
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
     * Get the records for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::getProxiedClass());
    }

    /**
     * Get the notifications for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::getProxiedClass(), 'notifiable')->latest();
    }

    /**
     * Get the avatar attribute.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        return sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5($this->email));
    }

    /**
     * Get the Root representation of the model.
     *
     * @return array
     */
    public function toRoot(): array
    {
        return array_merge($this->toArray(), [
            'avatar' => $this->avatar,
        ]);
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new Resource(static::class);
    }
}
