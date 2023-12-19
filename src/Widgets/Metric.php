<?php

namespace Cone\Root\Widgets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Metric extends Widget
{
    /**
     * The Eloquent query instance.
     */
    protected Builder $query;

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
     * Get the data.
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'data' => $this->calculate($request),
        ]);
    }
}
