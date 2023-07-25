<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Table\Columns\Columns;

trait ResolvesColumns
{
    /**
     * The resolved columns.
     */
    public readonly Columns $columns;

    /**
     * Define the columns for the object.
     */
    public function columns(): array
    {
        return [];
    }

    /**
     * Apply the given callback on the columns.
     */
    public function withColumns(Closure $callback): static
    {
        call_user_func_array($callback, [$this->columns, $this]);

        return $this;
    }
}
