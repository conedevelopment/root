<?php

namespace Cone\Root\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter implements Arrayable
{
    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, Request $request, mixed $value): Builder
    {
        $name = $this->getKey();

        if ($query->getModel()->hasNamedScope($name) && ! is_null($value)) {
            $query->getModel()->callNamedScope($name, [$query, $value]);
        }

        return $query;
    }

    /**
     * Get the URI key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return strtolower(class_basename(static::class));
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
       return [
           'key' => $this->getKey(),
       ];
    }
}
