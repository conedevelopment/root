<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Translation
{
    /**
     * Get the translatable model for the translation.
     */
    public function translatable(): MorphTo;
}
