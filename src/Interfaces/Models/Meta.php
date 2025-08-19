<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Meta
{
    /**
     * Get the metable model.
     */
    public function metable(): MorphTo;
}
