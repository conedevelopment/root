<?php

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Record
{
    /**
     * Get the user for the record.
     */
    public function user(): BelongsTo;

    /**
     * Get the target for the record.
     */
    public function target(): MorphTo;
}
