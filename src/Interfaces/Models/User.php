<?php

namespace Cone\Root\Interfaces\Models;

use Cone\Root\Models\AuthCode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface User
{
    /**
     * Get the uploads for the user.
     */
    public function uploads(): HasMany;

    /**
     * Get the Root notifications for the user.
     */
    public function rootNotifications(): MorphMany;

    /**
     * Get the current auth code for the user.
     */
    public function authCode(): HasOne;

    /**
     * Get the auth codes for the user.
     */
    public function authCodes(): HasMany;

    /**
     * Determine whether the object requires two factor authentitaction.
     */
    public function requiresTwoFactorAuthentication(): bool;

    /**
     * Generate a new auth code for the user.
     */
    public function generateAuthCode(): AuthCode;
}
