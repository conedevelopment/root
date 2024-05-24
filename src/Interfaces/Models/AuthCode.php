<?php

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AuthCode
{
    /**
     * Get the user for the model.
     */
    public function user(): BelongsTo;

    /**
     * Determine whether the code is active.
     */
    public function active(): bool;

    /**
     * Determine whether the code is expired.
     */
    public function expired(): bool;
}
