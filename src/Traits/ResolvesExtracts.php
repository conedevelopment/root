<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Extracts\Extracts;

trait ResolvesExtracts
{
    /**
     * The resolved extracts.
     */
    public readonly Extracts $extracts;

    /**
     * Define the extracts for the resource.
     */
    public function extracts(): array
    {
        return [];
    }

    /**
     * Apply the callback on the extracts.
     */
    public function withExtracts(Closure $callback): static
    {
        call_user_func_array($callback, [$this->extracts, $this]);

        return $this;
    }
}
