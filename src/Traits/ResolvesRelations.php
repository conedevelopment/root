<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Relations\Relations;

trait ResolvesRelations
{
    /**
     * The resolved relations.
     */
    public readonly Relations $relations;

    /**
     * Define the relations for the object.
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * Apply the callback on the relations.
     */
    public function withRelations(Closure $callback): static
    {
        call_user_func_array($callback, [$this->relations]);

        return $this;
    }
}
