<?php

namespace Cone\Root\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter implements Arrayable
{
    /**
     * Make a new filter instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): self
    {
        return new static(...$parameters);
    }

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
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->lower();
    }

    /**
     * The default value of the filter.
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return null;
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
           'default' => $this->getDefault(),
       ];
    }
}
