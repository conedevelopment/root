<?php

namespace Cone\Root\Models;

use Cone\Root\Interfaces\Models\User as Contract;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\HasMetaData;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements Contract
{
    use HasMedia;
    use HasMetaData;
    use InteractsWithProxy;
    use Notifiable;

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMorphClass(): string
    {
        return static::getProxiedClass();
    }

    /**
     * Get the uploads for the user.
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(Medium::getProxiedClass());
    }

    /**
     * Get the Root notifications for the user.
     */
    public function rootNotifications(): MorphMany
    {
        return $this->morphMany(Notification::getProxiedClass(), 'notifiable')->latest();
    }

    /**
     * Get the avatar attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function avatar(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): ?string {
                return isset($attributes['email'])
                    ? sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5($attributes['email']))
                    : null;
            }
        );
    }
}
