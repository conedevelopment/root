<?php

namespace Cone\Root\Interfaces\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Meta
{
    /**
     * Get the metable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function metable(): MorphTo;
}
