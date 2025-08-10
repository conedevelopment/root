<?php

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
