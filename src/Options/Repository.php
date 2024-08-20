<?php

namespace Cone\Root\Options;

use Cone\Root\Interfaces\Options\Repository as Contract;
use Cone\Root\Models\Option;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Repository implements Contract
{
    /**
     * The option cache.
     */
    protected Collection $cache;

    /**
     * Create a new repository instance.
     */
    public function __construct()
    {
        $this->cache = new Collection;
    }

    /**
     * Get the option query.
     */
    public function query(): Builder
    {
        return Option::proxy()->newQuery();
    }
}
