<?php

namespace Cone\Root\Interfaces;

interface TwoFactorAuthenticatable
{
    /**
     * Determine whether the object requires two factor authentitaction.
     */
    public function requiresTwoFactorAuthentication(): bool;
}
