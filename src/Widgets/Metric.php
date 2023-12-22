<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Metric extends Widget
{
    /**
     * The Eloquent query.
     */
    protected ?Builder $query = null;

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Calculate the metric data.
     */
    abstract public function calculate(Request $request): array;

    /**
     * Set the query.
     */
    public function setQuery(Builder $query): static
    {
        $this->query = $query->clone()->withoutEagerLoads();

        return $this;
    }

    /**
     * Set the query resolver.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->query)) {
            throw new QueryResolutionException();
        }

        return is_null($this->queryResolver)
            ? $this->query
            : call_user_func_array($this->queryResolver, [$request, $this->query]);
    }

    /**
     * Get the data.
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'data' => $this->calculate($request),
        ]);
    }
}
