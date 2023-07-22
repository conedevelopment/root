<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Illuminate\Database\Eloquent\Builder;

trait ResolvesQuery
{
    /**
     * The resolved query instance.
     */
    protected ?Builder $query = null;

    /**
     * The query resolver.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Set the query resolver.
     */
    public function query(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query for the table.
     */
    public function resolveQuery(): Builder
    {
        if (is_null($this->query) && is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        } elseif (is_null($this->query)) {
            $this->query = call_user_func($this->queryResolver);
        }

        return $this->query;
    }
}
