<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Table\Filters\Filters;
use Cone\Root\Table\Filters\Search;
use Cone\Root\Table\Filters\TrashStatus;

trait ResolvesFilters
{
    /**
     * The resolved filters.
     */
    public readonly Filters $filters;

    /**
     * Define the filters for the resource.
     */
    public function filters(): array
    {
        return [
            new Search($this),
            // new Sort($this),
            new TrashStatus($this),
        ];
    }

    /**
     * Apply the given callback on the filters.
     */
    public function withFilters(Closure $callback): static
    {
        call_user_func_array($callback, [$this->filters, $this]);

        return $this;
    }
}
