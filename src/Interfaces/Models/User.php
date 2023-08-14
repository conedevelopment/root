<?php

namespace Cone\Root\Interfaces\Models;

use Cone\Root\Interfaces\Resourceable;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface User extends Resourceable
{
    /**
     * Get the uploads for the user.
     */
    public function uploads(): HasMany;
}
