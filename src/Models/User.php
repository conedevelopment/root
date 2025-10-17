<?php

declare(strict_types=1);

namespace Cone\Root\Models;

use Cone\Root\Interfaces\Models\User as Contract;
use Cone\Root\Traits\HasMedia;
use Cone\Root\Traits\HasMetaData;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
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
     * Get the current auth code for the user.
     */
    public function authCode(): HasOne
    {
        return $this->authCodes()->one()->ofMany()->active();
    }

    /**
     * Get the auth codes for the user.
     */
    public function authCodes(): HasMany
    {
        return $this->hasMany(AuthCode::getProxiedClass())->active();
    }

    /**
     * Get the triggered root events for the user.
     */
    public function triggeredRootEvents(): HasMany
    {
        return $this->hasMany(Event::getProxiedClass());
    }

    /**
     * Determine whether the object requires two factor authentication.
     */
    public function requiresTwoFactorAuthentication(): bool
    {
        return false;
    }

    /**
     * Determine whether the user should be two factor authenticated.
     */
    public function shouldTwoFactorAuthenticate(Request $request): bool
    {
        if (! $this->requiresTwoFactorAuthentication()) {
            return false;
        }

        if ($request->cookie('device_token') === $this->generateDeviceToken($request)) {
            return false;
        }

        return ! $request->session()->has('root.auth.two-factor');
    }

    /**
     * Generate a device token.
     */
    public function generateDeviceToken(Request $request): string
    {
        return sha1(sprintf('%s:%s', $request->user()->getKey(), $request->user()->email));
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
