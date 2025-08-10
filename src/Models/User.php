<?php

namespace Cone\Root\Models;

use Cone\Laravel\Auth\Traits\HasAuthCodes;
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
    use HasAuthCodes;
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
     * Get the triggered root events for the user.
     */
    public function triggeredRootEvents(): HasMany
    {
        return $this->hasMany(Event::getProxiedClass());
    }

    /**
     * Get the avatar attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    protected function avatar(): Attribute
    {
        return new Attribute(
            get: static fn (mixed $value, array $attributes): ?string => isset($attributes['email'])
                ? sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5($attributes['email']))
                : null
        );
    }
}
