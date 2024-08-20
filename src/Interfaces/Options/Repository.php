<?php

namespace Cone\Root\Interfaces\Options;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface Repository
{
    /**
     * Get the option query.
     */
    public function query(): Builder;
}
