<?php

namespace Cone\Root\Traits;

use Cone\Root\Interfaces\Models\User;
use Cone\Root\Models\Medium;
use Cone\Root\Models\Notification;
use Cone\Root\Models\Record;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait AsRootUser
{
    use InteractsWithProxy;
    use HasMetaData;

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return User::class;
    }

    /**
     * Get the uploads for the user.
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(Medium::getProxiedClass());
    }

    /**
     * Get the records for the user.
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::getProxiedClass());
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
     */
    protected function avatar(): Attribute
    {
        return new Attribute(get: static function (mixed $value, array $attributes): ?string {
            return isset($attributes['email']) ? sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5($attributes['email'])) : null;
        });
    }
}
